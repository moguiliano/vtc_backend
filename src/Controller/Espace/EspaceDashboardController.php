<?php

namespace App\Controller\Espace;

use App\Entity\CompteAbonne;
use App\Entity\Societe;
use App\Enum\EtatAbonne;
use App\Form\SocieteAbonneType;
use App\Repository\GrilleTarifRepository;
use App\Repository\SocieteRepository;
use App\Service\EntrepriseCourante;
use App\Service\GrilleAbonnes;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Espace abonné en style EasyAdmin (D-076, D-092) : 2ᵉ tableau de bord, séparé de /admin.
 *
 * allowedControllers: [] → AUCUN écran CRUD de l'admin n'existe sous /espace (verrou n°3, D-078).
 * Les pages sont des méthodes de ce contrôleur : EasyAdmin leur donne le menu et la mise en page.
 * Elles lisent toujours l'entreprise du compte connecté (EntrepriseCourante), jamais un numéro dans l'adresse.
 */
#[AdminDashboard(routePath: '/espace', routeName: 'espace', allowedControllers: [])]
class EspaceDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly SocieteRepository $societeRepo,
        private readonly EntityManagerInterface $em,
    ) {}

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<span style="color:#ff5630;font-weight:800;">ZenCAR</span> <small style="font-size:.7em;opacity:.7;">Espace pro</small>')
            ->setFaviconPath('favicon.ico')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        $entreprise = $this->entrepriseCourante->get();

        yield MenuItem::linkToRoute('Accueil', 'fa fa-home', 'espace_accueil');
        yield MenuItem::section('Mon activité');
        yield MenuItem::linkToRoute('Ma société', 'fa fa-building', 'espace_ma_societe');
        if (!$entreprise->isEnAttente()) {
            yield MenuItem::linkToRoute('Mes prix', 'fa fa-tags', 'espace_mes_prix');
        }
        yield MenuItem::section('Mon compte');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }

    public function configureUserMenu(UserInterface $user): UserMenu
    {
        return parent::configureUserMenu($user)
            ->setName($user instanceof CompteAbonne ? (string) $user->getEntreprise()?->getNom() : $user->getUserIdentifier())
            ->displayUserAvatar(false);
    }

    #[Route('/espace', name: 'espace_accueil', methods: ['GET'])]
    public function index(): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $societe = $this->societeRepo->trouverPourEntreprise($entreprise);

        // Avant validation : seul « Ma société » est accessible (D-015).
        if ($entreprise->isEnAttente()) {
            return $societe
                ? $this->render('espace/ea/en_attente.html.twig', ['entreprise' => $entreprise, 'societe' => $societe])
                : $this->redirectToRoute('espace_ma_societe');
        }

        return $this->render('espace/ea/accueil.html.twig', ['entreprise' => $entreprise]);
    }

    #[Route('/espace/ma-societe', name: 'espace_ma_societe', methods: ['GET', 'POST'])]
    public function maSociete(Request $request): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $societe = $this->societeRepo->trouverPourEntreprise($entreprise);

        // Après validation, l'abonné ne modifie plus sa société lui-même (D-078).
        if (!$entreprise->isEnAttente()) {
            return $this->render('espace/ea/ma_societe.html.twig', [
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

        return $this->render('espace/ea/ma_societe.html.twig', [
            'entreprise' => $entreprise,
            'societe' => $societe,
            'form' => $form,
        ]);
    }

    /**
     * « Mes prix » (étape 3, D-086 à D-091) : véhicules proposés, 4 prix par véhicule, majoration de nuit.
     * Ne lit et n'écrit que les grilles de l'abonné connecté (tri automatique + aucune id d'entreprise dans l'adresse).
     */
    #[Route('/espace/mes-prix', name: 'espace_mes_prix', methods: ['GET', 'POST'])]
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
            return $this->render('espace/ea/mes_prix.html.twig', [
                'entreprise' => $this->entrepriseCourante->get(), 'grilles' => $grilleRepo->findBy(['entreprise' => $this->entrepriseCourante->get()]),
                'modifiable' => $modifiable, 'erreurs' => $erreurs, 'saisis' => $saisis,
            ], new Response('', 422));
        }

        return $this->render('espace/ea/mes_prix.html.twig', [
            'entreprise' => $entreprise,
            'grilles' => $grilles,
            'modifiable' => $modifiable,
            'erreurs' => $erreurs,
            'saisis' => null,
        ]);
    }
}
