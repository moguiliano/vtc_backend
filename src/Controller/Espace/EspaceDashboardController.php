<?php

namespace App\Controller\Espace;

use App\Entity\CompteAbonne;
use App\Entity\GrilleTarif;
use App\Entity\Reservation;
use App\Enum\StatutChauffeur;
use App\Service\CoursesRecues;
use App\Service\PartageCourseService;
use App\Entity\Societe;
use App\Enum\EtatAbonne;
use App\Form\SocieteAbonneType;
use App\Repository\SocieteRepository;
use App\Service\EntrepriseCourante;
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
 * allowedControllers : seulement les écrans de l'espace (Mes prix) → AUCUN écran CRUD de l'admin n'existe sous /espace (verrou n°3, D-078).
 * Les pages sont des méthodes de ce contrôleur : EasyAdmin leur donne le menu et la mise en page.
 * Elles lisent toujours l'entreprise du compte connecté (EntrepriseCourante), jamais un numéro dans l'adresse.
 */
#[AdminDashboard(routePath: '/espace', routeName: 'espace', allowedControllers: [MesPrixCrudController::class, MesCoursesCrudController::class])]
class EspaceDashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly SocieteRepository $societeRepo,
        private readonly EntityManagerInterface $em,
        private readonly CoursesRecues $coursesRecues,
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
        if (!$entreprise->isEnAttente()) {
            yield MenuItem::section('Mes courses');
            if ($entreprise->getEtat() !== EtatAbonne::LECTURE_SEULE) {
                yield MenuItem::linkToRoute('Nouvelle course', 'fa fa-plus-circle', 'espace_nouvelle_course');
            }
            yield MenuItem::linkToCrud('Mes courses', 'fa fa-calendar-check', Reservation::class)->setController(MesCoursesCrudController::class);
            // Courses confiées par ZenCAR (D-100) : pastille = en attente de sa réponse
            $aRepondre = $this->coursesRecues->enAttenteDeReponse($entreprise);
            $recues = MenuItem::linkToRoute('Courses reçues', 'fa fa-inbox', 'espace_courses_recues');
            yield $aRepondre > 0 ? $recues->setBadge($aRepondre, 'danger') : $recues;
        }
        yield MenuItem::section('Mon activité');
        yield MenuItem::linkToRoute('Ma société', 'fa fa-building', 'espace_ma_societe');
        if (!$entreprise->isEnAttente()) {
            yield MenuItem::linkToCrud('Mes prix', 'fa fa-tags', GrilleTarif::class)->setController(MesPrixCrudController::class);
            yield MenuItem::linkToRoute('Ma majoration de nuit', 'fa fa-moon', 'espace_ma_nuit');
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

    /** « Ma majoration de nuit » (D-089, D-093) : oui / non + %, heures communes 23 h → 7 h. */
    #[Route('/espace/ma-nuit', name: 'espace_ma_nuit', methods: ['GET', 'POST'])]
    public function maNuit(Request $request): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        if ($entreprise->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }
        $modifiable = $entreprise->getEtat() !== EtatAbonne::LECTURE_SEULE;
        $erreur = null;

        if ($request->isMethod('POST') && $modifiable) {
            $pourcent = str_replace(',', '.', trim((string) $request->request->get('nuit_pourcent')));
            if (!$this->isCsrfTokenValid('ma-nuit', (string) $request->request->get('_token'))) {
                $erreur = 'Le formulaire a expiré, recommencez.';
            } elseif (!is_numeric($pourcent) || (float) $pourcent < 0 || (float) $pourcent > 100) {
                $erreur = 'Indiquez un pourcentage entre 0 et 100.';
            } else {
                $entreprise->setNuitActive($request->request->has('nuit_active'))->setNuitPourcent((float) $pourcent);
                $this->em->flush();
                $this->addFlash('success', 'Majoration de nuit enregistrée.');

                return $this->redirectToRoute('espace_ma_nuit');
            }
        }

        return $this->render('espace/ea/ma_nuit.html.twig', [
            'entreprise' => $entreprise,
            'modifiable' => $modifiable,
            'erreur' => $erreur,
        ]);
    }

    /** « Courses reçues » (étape 2b, D-100 à D-107) : seulement celles où il est l'abonné retenu en ce moment. */
    #[Route('/espace/courses-recues', name: 'espace_courses_recues', methods: ['GET'])]
    public function coursesRecues(PartageCourseService $partage): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        if ($entreprise->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }

        return $this->render('espace/ea/courses_recues.html.twig', [
            'courses' => $this->coursesRecues->liste($entreprise),
            'partage' => $partage,
        ]);
    }

    #[Route('/espace/courses-recues/{id}', name: 'espace_course_recue', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function courseRecue(int $id, PartageCourseService $partage): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $r = $entreprise->isEnAttente() ? null : $this->coursesRecues->trouver($entreprise, $id);
        if (!$r) {
            throw $this->createNotFoundException(); // pas (ou plus) à lui : introuvable (D-100)
        }

        return $this->render('espace/ea/course_recue.html.twig', [
            'r' => $r,
            'partage' => $partage,
            'boutons' => $entreprise->getEtat() === EtatAbonne::LECTURE_SEULE ? [] : StatutChauffeur::suivants($r->getStatutChauffeur()),
        ]);
    }

    /** Réponse du chauffeur : la seule écriture possible sur une course confiée (D-100, D-102, D-106). */
    #[Route('/espace/courses-recues/{id}/statut', name: 'espace_course_recue_statut', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function courseRecueStatut(int $id, Request $request): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $statut = StatutChauffeur::tryFrom((string) $request->request->get('statut'));

        if (!$this->isCsrfTokenValid('course-recue-' . $id, (string) $request->request->get('_token'))) {
            $this->addFlash('danger', 'Le formulaire a expiré, recommencez.');
        } elseif ($entreprise->isEnAttente() || $entreprise->getEtat() === EtatAbonne::LECTURE_SEULE || !$statut
            || !$this->coursesRecues->repondre($entreprise, $id, $statut)) {
            $this->addFlash('warning', 'Action impossible sur cette course.');
        } else {
            $this->addFlash('success', 'Statut enregistré : ' . $statut->label() . '.');
        }

        return $this->coursesRecues->trouver($entreprise, $id)
            ? $this->redirectToRoute('espace_course_recue', ['id' => $id])
            : $this->redirectToRoute('espace_courses_recues');
    }
}
