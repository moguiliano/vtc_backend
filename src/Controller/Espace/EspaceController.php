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
}
