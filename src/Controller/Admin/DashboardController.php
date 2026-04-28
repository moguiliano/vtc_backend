<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Contact;
use App\Entity\Forfait;
use App\Entity\Reservation;
use App\Entity\VehicleCategory;
use App\Repository\ContactRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private AdminUrlGenerator $adminUrlGenerator,
        private ContactRepository $contactRepo,
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
            yield MenuItem::linkToCrud('Réservations', 'fa fa-calendar-check', Reservation::class);
        }

        // Messages de contact
        $unread = $this->contactRepo->countUnread();
        $label  = 'Messages reçus' . ($unread > 0 ? ' <span style="background:#ff5630;color:#fff;border-radius:10px;padding:1px 7px;font-size:11px;margin-left:4px;">' . $unread . '</span>' : '');
        yield MenuItem::section('Contact');
        yield MenuItem::linkToCrud($label, 'fa fa-envelope', Contact::class);

        // Tarification
        if ($this->isGranted('ROLE_FORFAITS_VIEW') || $this->isGranted('ROLE_VEHICULES_VIEW')) {
            yield MenuItem::section('Tarification');
        }
        if ($this->isGranted('ROLE_VEHICULES_VIEW')) {
            yield MenuItem::linkToCrud('Véhicules & Prix', 'fa fa-car', VehicleCategory::class);
        }
        if ($this->isGranted('ROLE_FORFAITS_VIEW')) {
            yield MenuItem::linkToCrud('Forfaits VTC', 'fa fa-tag', Forfait::class);
        }

        // Gestion des admins (super admin uniquement)
        if ($this->isGranted('ROLE_SUPER_ADMIN')) {
            yield MenuItem::section('Administration');
            yield MenuItem::linkToCrud('Administrateurs', 'fa fa-users', Admin::class);
        }

        // Mon compte (tous)
        yield MenuItem::section('Mon compte');
        yield MenuItem::linkToRoute('Changer mon mot de passe', 'fa fa-key', 'admin_change_password');
        yield MenuItem::linkToLogout('Déconnexion', 'fa fa-sign-out');
    }
}
