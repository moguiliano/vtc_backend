<?php

namespace App\Controller\Espace;

use App\Entity\CompteAbonne;
use App\Entity\Entreprise;
use App\Entity\Societe;
use App\Form\InscriptionAbonneType;
use App\Form\SocieteAbonneType;
use App\Repository\GrilleTarifRepository;
use App\Repository\SocieteRepository;
use App\Service\GrilleAbonnes;
use App\Enum\EtatAbonne;
use App\Service\EntrepriseCourante;
use App\Service\LimiteInscriptions;
use App\Service\PhoneNormalizerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Espace abonné, étape 1 (D-073 à D-078) : inscription, connexion, « Ma société », accueil.
 *
 * Cloisonnement : toutes les pages lisent l'entreprise via EntrepriseCourante (= celle du compte connecté),
 * jamais via un numéro dans l'adresse.
 */
#[Route('/espace')]
class EspaceController extends AbstractController
{
    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly SocieteRepository $societeRepo,
        private readonly EntityManagerInterface $em,
    ) {}

    #[Route('/inscription', name: 'espace_inscription', methods: ['GET', 'POST'])]
    public function inscription(
        Request $request,
        LimiteInscriptions $limite,
        UserPasswordHasherInterface $hasher,
        PhoneNormalizerService $phones,
        ValidatorInterface $validator,
        Security $security,
    ): Response {
        if ($this->getUser() instanceof CompteAbonne) {
            return $this->redirectToRoute('espace_accueil');
        }

        $form = $this->createForm(InscriptionAbonneType::class);
        $form->handleRequest($request);
        $erreur = null;

        if ($form->isSubmitted()) {
            $data = $form->getData();

            // Piège à robots (D-075) : on fait comme si tout allait bien, sans rien créer.
            if (!empty($data['siteWeb'])) {
                $this->addFlash('info', 'Merci. Connectez-vous pour continuer.');
                return $this->redirectToRoute('espace_connexion');
            }

            if ($limite->estDepassee($request->getClientIp())) {
                $erreur = 'Trop d\'inscriptions depuis votre connexion. Réessayez dans une heure.';
            } elseif ($form->isValid()) {
                $entreprise = (new Entreprise())
                    ->setNom(trim($data['nom']))
                    ->setTypeAbonnement($data['typeAbonnement']);

                $compte = (new CompteAbonne())
                    ->setEntreprise($entreprise)
                    ->setEmail($data['email'])
                    ->setTelephone((string) $phones->normalize($data['telephone']));
                $compte->setPassword($hasher->hashPassword($compte, $data['motDePasse']));

                $violations = $validator->validate($compte);
                if (count($violations) > 0) {
                    $erreur = $violations[0]->getMessage();
                } else {
                    $this->em->persist($entreprise);
                    $this->em->persist($compte);
                    $this->em->flush();
                    $limite->compter($request->getClientIp());

                    $security->login($compte, 'form_login', 'espace');

                    return $this->redirectToRoute('espace_ma_societe');
                }
            }
        }

        return $this->render('espace/inscription.html.twig', [
            'form' => $form,
            'erreur' => $erreur,
        ]);
    }

    #[Route('/connexion', name: 'espace_connexion', methods: ['GET', 'POST'])]
    public function connexion(AuthenticationUtils $auth): Response
    {
        if ($this->getUser() instanceof CompteAbonne) {
            return $this->redirectToRoute('espace_accueil');
        }

        return $this->render('espace/connexion.html.twig', [
            'error' => $auth->getLastAuthenticationError(),
            'last_username' => $auth->getLastUsername(),
        ]);
    }

    #[Route('/deconnexion', name: 'espace_deconnexion')]
    public function deconnexion(): never
    {
        throw new \LogicException('Géré par le pare-feu « espace ».');
    }

    #[Route('', name: 'espace_accueil', methods: ['GET'])]
    public function accueil(): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $societe = $this->societeRepo->trouverPourEntreprise($entreprise);

        // Avant validation : seul « Ma société » est accessible (D-015).
        if ($entreprise->isEnAttente()) {
            return $societe
                ? $this->render('espace/en_attente.html.twig', ['entreprise' => $entreprise, 'societe' => $societe])
                : $this->redirectToRoute('espace_ma_societe');
        }

        return $this->render('espace/accueil.html.twig', [
            'entreprise' => $entreprise,
            'societe' => $societe,
        ]);
    }

    #[Route('/ma-societe', name: 'espace_ma_societe', methods: ['GET', 'POST'])]
    public function maSociete(Request $request): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $societe = $this->societeRepo->trouverPourEntreprise($entreprise);

        // Après validation, l'abonné ne modifie plus sa société lui-même (D-078).
        if (!$entreprise->isEnAttente()) {
            return $this->render('espace/ma_societe.html.twig', [
                'entreprise' => $entreprise,
                'societe' => $societe,
                'form' => null,
            ]);
        }

        $nouvelle = $societe === null;
        $societe ??= Societe::vierge($entreprise);

        $form = $this->createForm(SocieteAbonneType::class, $societe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $societe->setEntreprise($entreprise)->touch();
            if ($nouvelle) {
                $this->em->persist($societe);
            }
            $this->em->flush();
            $this->addFlash('success', 'Merci ! Vos informations sont envoyées. Vous recevrez l\'accès complet dès leur validation.');

            return $this->redirectToRoute('espace_accueil');
        }

        return $this->render('espace/ma_societe.html.twig', [
            'entreprise' => $entreprise,
            'societe' => $societe,
            'form' => $form,
        ]);
    }

    /**
     * « Mes prix » (étape 3, D-086 à D-091) : véhicules proposés, 4 prix par véhicule, majoration de nuit.
     * Ne lit et n'écrit que les grilles de l'abonné connecté (tri automatique + aucune id d'entreprise dans l'adresse).
     */
    #[Route('/mes-prix', name: 'espace_mes_prix', methods: ['GET', 'POST'])]
    public function mesPrix(Request $request, GrilleTarifRepository $grilleRepo, GrilleAbonnes $grilleAbonnes): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        if ($entreprise->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }
        $modifiable = $entreprise->getEtat() !== EtatAbonne::LECTURE_SEULE;

        $grilleAbonnes->creerPour($entreprise); // filet : lignes manquantes (nouvelle catégorie…)
        $grilles = $grilleRepo->createQueryBuilder('g')
            ->join('g.categorie', 'c')->addSelect('c')
            ->where('g.entreprise = :e')->setParameter('e', $entreprise)
            ->andWhere('c.isActive = true')
            ->orderBy('c.displayOrder', 'ASC')
            ->getQuery()->getResult();
        $erreurs = [];

        if ($request->isMethod('POST') && $modifiable) {
            if (!$this->isCsrfTokenValid('mes-prix', (string) $request->request->get('_token'))) {
                $this->addFlash('info', 'Le formulaire a expiré, recommencez.');
                return $this->redirectToRoute('espace_mes_prix');
            }

            // « Revenir aux prix ZenCAR » pour un véhicule (D-091)
            $raccrocher = (int) $request->request->get('raccrocher');
            if ($raccrocher > 0) {
                foreach ($grilles as $g) {
                    if ($g->getId() === $raccrocher) {
                        $grilleAbonnes->raccrocher($g);
                        $this->addFlash('success', sprintf('%s : prix ZenCAR remis.', $g->getCategorie()->getLabel()));
                    }
                }
                return $this->redirectToRoute('espace_mes_prix');
            }

            $actifs = (array) $request->request->all('actif');
            $saisis = (array) $request->request->all('prix');
            $lire = fn ($v) => is_numeric($v = str_replace([',', ' ', '€'], ['.', '', ''], (string) $v)) ? (float) $v : null;

            foreach ($grilles as $g) {
                $id = $g->getId();
                $label = $g->getCategorie()->getLabel();
                $v = array_map($lire, [
                    $saisis[$id]['seuil'] ?? null, $saisis[$id]['pec'] ?? null,
                    $saisis[$id]['km_sous'] ?? null, $saisis[$id]['km_sur'] ?? null,
                ]);
                if (in_array(null, $v, true) || min($v) < 0 || max($v) > 1000) {
                    $erreurs[] = sprintf('%s : chaque prix doit être un nombre entre 0 et 1000.', $label);
                    continue;
                }
                $actif = isset($actifs[$id]);
                if ($actif && $v[1] <= 0 && $v[2] <= 0) {
                    $erreurs[] = sprintf('%s : indiquez au moins une prise en charge ou un prix au km.', $label);
                    continue;
                }
                // Un chiffre changé = l'abonné fixe ses propres prix : ce véhicule ne suit plus ZenCAR (D-091)
                $avant = array_map(fn ($x) => round($x, 2), $g->prix());
                $apres = array_map(fn ($x) => round($x, 2), $v);
                if ($avant !== $apres) {
                    $g->setPrix(...$v)->setSuitZencar(false);
                }
                $g->setActif($actif)->setUpdatedAt(new \DateTimeImmutable());
            }

            $nuitPourcent = $lire($request->request->get('nuit_pourcent'));
            if ($nuitPourcent === null || $nuitPourcent < 0 || $nuitPourcent > 100) {
                $erreurs[] = 'Majoration de nuit : un pourcentage entre 0 et 100.';
            } else {
                $entreprise->setNuitActive($request->request->has('nuit_active'))->setNuitPourcent($nuitPourcent);
            }

            if (!$erreurs) {
                $this->em->flush();
                $this->addFlash('success', 'Vos prix sont enregistrés.');
                return $this->redirectToRoute('espace_mes_prix');
            }
            $this->em->clear(); // rien n'est enregistré si une ligne est fausse
            return $this->render('espace/mes_prix.html.twig', [
                'entreprise' => $this->entrepriseCourante->get(), 'grilles' => $grilleRepo->findBy(['entreprise' => $this->entrepriseCourante->get()]),
                'modifiable' => $modifiable, 'erreurs' => $erreurs, 'saisis' => $saisis,
            ], new Response('', 422));
        }

        return $this->render('espace/mes_prix.html.twig', [
            'entreprise' => $entreprise,
            'grilles' => $grilles,
            'modifiable' => $modifiable,
            'erreurs' => $erreurs,
            'saisis' => null,
        ]);
    }
}
