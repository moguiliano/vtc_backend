<?php

namespace App\Controller\Admin;

use App\Controller\Espace\MesCoursesCrudController;
use App\Controller\Espace\MesPrixCrudController;
use App\Entity\Admin;
use App\Entity\Appel;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Entity\Contact;
use App\Entity\Entreprise;
use App\Enum\EtatAbonne;
use App\Repository\EntrepriseRepository;
use App\Repository\ReservationRepository;
use App\Enum\StatutChauffeur;
use App\Entity\Facture;
use App\Entity\Forfait;
use App\Entity\GrilleTarif;
use App\Entity\Reservation;
use App\Entity\Societe;
use App\Entity\MessageCategory;
use App\Entity\MessageTemplate;
use App\Entity\SiteSettings;
use App\Entity\VehicleCategory;
use App\Repository\ContactRepository;
use App\Repository\SocieteRepository;
use App\Service\EntrepriseCourante;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

// Les écrans de l'espace abonné n'existent pas sous /admin (D-092, D-093)
#[AdminDashboard(routePath: '/admin', routeName: 'admin', deniedControllers: [MesPrixCrudController::class, MesCoursesCrudController::class])]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private ContactRepository $contactRepo,
        private SocieteRepository $societeRepo,
        private EntrepriseCourante $entrepriseCourante,
        private EntrepriseRepository $entrepriseRepo,
        private ReservationRepository $reservationRepo,
    ) {}

    #[Route('/admin', name: 'admin_dashboard')]
    public function index(): Response
    {
        // Redirige vers les réservations si accès, sinon tableau de bord
        if ($this->isGranted('ROLE_RESERVATIONS_VIEW')) {
            $url = $this->adminUrlGenerator
                ->setController(ReservationCrudController::class)
                ->generateUrl();
            return $this->redirect($url);
        }

        return $this->render('@EasyAdmin/page/content.html.twig', [
            'content_title' => 'Tableau de bord ZenCAR',
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('ZenCAR — Administration')
            ->renderContentMaximized();
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Tableau de bord', 'fa fa-home');

        // Réservations
        if ($this->isGranted('ROLE_RESERVATIONS_VIEW')) {
            yield MenuItem::section('Réservations');
            // Pastille : courses dont l'abonné retenu s'est retiré, à réattribuer (D-102, D-107)
            $retires = $this->reservationRepo->count(['statutChauffeur' => StatutChauffeur::RETIRE]);
            $reservations = MenuItem::linkToCrud('Réservations', 'fa fa-calendar-check', Reservation::class);
            yield $retires > 0 ? $reservations->setBadge($retires . ' retrait' . ($retires > 1 ? 's' : ''), 'danger') : $reservations;
            yield MenuItem::linkToCrud('Factures', 'fa fa-file-invoice', Facture::class);
        }
        if ($this->isGranted('ROLE_RESERVATIONS_EDIT')) {
            yield MenuItem::linkToRoute('Nouvelle réservation (téléphone)', 'fa fa-phone', 'admin_phone_booking');
            yield MenuItem::linkToRoute('Réservation rapide', 'fa fa-bolt', 'admin_quick_book');
        }

        // Journal des appels (suivi rentabilité Google Ads)
        if ($this->isGranted('ROLE_ADMIN')) {
            yield MenuItem::section('Suivi des appels');
            yield MenuItem::linkToCrud('Noter un appel', 'fa fa-phone-volume', Appel::class)
                ->setAction(Crud::PAGE_NEW);
            yield MenuItem::linkToCrud('Journal des appels', 'fa fa-list', Appel::class);
        }

        // Messages de contact
        $unread = $this->contactRepo->countUnread();
        $label  = 'Messages reçus' . ($unread > 0 ? ' <span style="background:#ff5630;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">' . $unread . '</span>' : '');
        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud($label, 'fa fa-envelope', Contact::class);

        // Tarification (super-admin uniquement, D-021)
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Tarification');
        }
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Grille de prix', 'fa fa-euro-sign', GrilleTarif::class);
            yield MenuItem::linkToCrud('Véhicules', 'fa fa-car', VehicleCategory::class);
        }
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::linkToCrud('Forfaits VTC', 'fa fa-tag', Forfait::class);
        }

        // Réglages du site visibles par les clients (super admin uniquement)
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Site');
            yield MenuItem::linkToCrud('Avis Google', 'fa fa-star', SiteSettings::class);
            // Textes de « Partager la course » (D-030)
            yield MenuItem::subMenu('Messages', 'fa fa-comment-dots')->setSubItems([
                MenuItem::linkToCrud('Catégories', 'fa fa-folder', MessageCategory::class),
                MenuItem::linkToCrud('Modèles de messages', 'fa fa-file-alt', MessageTemplate::class),
            ]);
        }

        // Gestion des admins (super admin uniquement)
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Administration');
            yield MenuItem::linkToCrud('Administrateurs', 'fa fa-users', Admin::class);
            // Espaces abonnés (D-074 à D-078) : badge = sociétés en attente de validation
            $enAttente = $this->entrepriseRepo->count(['etat' => EtatAbonne::EN_ATTENTE]);
            yield MenuItem::linkToCrud('Abonnés' . ($enAttente > 0 ? ' <span style="background:#ff5630;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">' . $enAttente . '</span>' : ''), 'fa fa-id-badge', Entreprise::class)
                ->setController(AbonneCrudController::class);
            yield MenuItem::linkToCrud('Ma société', 'fa fa-building', Societe::class)
                ->setAction(Action::EDIT)
                ->setEntityId($this->societeRepo->pourEntreprise($this->entrepriseCourante->get())->getId());
        }

        // Mon compte (tous)
        yield MenuItem::section('Mon compte');
        yield MenuItem::linkToRoute('Changer mon mot de passe', 'fa fa-key', 'admin_change_password');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
