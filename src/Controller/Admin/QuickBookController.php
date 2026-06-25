<?php

namespace App\Controller\Admin;

use App\Entity\Reservation;
use App\Repository\ForfaitRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    ) {}

    #[Route('/admin/quick-book', name: 'admin_quick_book')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        $forfaits = $this->forfaitRepo->findBy(['actif' => true], ['ordre' => 'ASC']);
        $error    = null;

        if ($request->isMethod('POST')) {
            $forfaitId = (int) $request->request->get('forfaitId');
            $prenom    = trim($request->request->get('prenom', ''));
            $phone     = trim($request->request->get('telephone', ''));
            $mode      = in_array($request->request->get('modeReglement'), ['especes', 'carte_bancaire'], true)
                         ? $request->request->get('modeReglement') : 'carte_bancaire';
            $offset    = max(-60, min(0, (int) $request->request->get('offsetMinutes', 0)));

            $forfait = $this->forfaitRepo->find($forfaitId);

            if ($forfait && $prenom && $phone) {
                $reservation = new Reservation();
                $reservation->setDepart($forfait->getDepart());
                $reservation->setArrivee($forfait->getArrivee());
                $reservation->setPrix((float) $forfait->getPrix());
                $reservation->setDistance($forfait->getDistance());
                $reservation->setDuree($forfait->getDuree());
                $reservation->setTypeVehicule('eco_berline');
                $reservation->setDateHeureDepart(new \DateTime("{$offset} minutes"));
                $reservation->setStopOption(false);
                $reservation->setSiegeBebe(false);
                $reservation->setGuestPrenom(mb_substr($prenom, 0, 100));
                $reservation->setGuestTelephone(mb_substr($phone, 0, 25));
                $reservation->setGuestInfo(json_encode(['prenom' => $prenom, 'phone' => $phone]));
                $reservation->setModeReglement($mode);
                $reservation->setIsGuest(true);
                $reservation->setStatut('confirmee');

                $em->persist($reservation);
                $em->flush();

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
