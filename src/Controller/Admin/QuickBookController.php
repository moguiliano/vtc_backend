<?php

namespace App\Controller\Admin;

use App\Repository\ForfaitRepository;
use App\Service\EntrepriseCourante;
use App\Service\ReservationCreator;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_RESERVATIONS_EDIT')]
class QuickBookController extends AbstractController
{
    public function __construct(
        private ForfaitRepository $forfaitRepo,
        private AdminUrlGenerator $adminUrlGenerator,
        private ReservationCreator $creator,
        private EntrepriseCourante $entrepriseCourante,
    ) {}

    #[Route('/admin/quick-book', name: 'admin_quick_book')]
    public function index(Request $request): Response
    {
        $entreprise = $this->entrepriseCourante->get();
        $forfaits   = $this->forfaitRepo->findBy(['actif' => true, 'entreprise' => $entreprise], ['ordre' => 'ASC']);
        $error    = null;

        if ($request->isMethod('POST')) {
            $forfaitId = (int) $request->request->get('forfaitId');
            $prenom    = trim($request->request->get('prenom', ''));
            $phone     = trim($request->request->get('telephone', ''));
            $mode      = $request->request->get('modeReglement');
            $offset    = (int) $request->request->get('offsetMinutes', 0);

            $forfait = $this->forfaitRepo->findOneBy(['id' => $forfaitId, 'entreprise' => $entreprise]);

            if ($forfait && $prenom && $phone) {
                $reservation = $this->creator->depuisForfait($forfait, $prenom, $phone, $mode, $offset);

                $url = $this->adminUrlGenerator
                    ->setController(ReservationCrudController::class)
                    ->setAction('detail')
                    ->setEntityId($reservation->getId())
                    ->generateUrl();

                return $this->redirect($url);
            }

            $error = 'Merci de remplir tous les champs obligatoires.';
        }

        return $this->render('admin/quick_book.html.twig', [
            'forfaits' => $forfaits,
            'error'    => $error,
        ]);
    }
}
