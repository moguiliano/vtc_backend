<?php

namespace App\Controller\Admin;

use App\Entity\Facture;
use App\Repository\FactureRepository;
use App\Repository\ReservationRepository;
use App\Service\EntrepriseCourante;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\HttpFoundation\RequestStack;

class FactureCrudController extends AbstractCrudController
{
    public function __construct(
        private FactureRepository $factureRepo,
        private ReservationRepository $reservationRepo,
        private RequestStack $requestStack,
        private EntrepriseCourante $entrepriseCourante,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Facture::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Facture')
            ->setEntityLabelInPlural('Factures')
            ->setDefaultSort(['numero' => 'DESC'])
            ->setSearchFields(['numero', 'clientNom', 'clientSiret']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $pdf = Action::new('facturePdf', 'PDF', 'fa fa-file-pdf')
            ->linkToRoute('admin_facture_pdf', fn(Facture $f) => ['id' => $f->getId()])
            ->setHtmlAttributes(['target' => '_blank'])
            ->addCssClass('btn btn-secondary');

        // Numérotation continue : une facture émise ne se supprime pas
        $actions->disable(Action::DELETE, Action::BATCH_DELETE);

        // D-065 : le régulateur voit les factures (liste, détail, PDF) sans les créer ni les modifier
        if (!$this->isGranted('ROLE_SUPER_ADMIN')) {
            $actions->disable(Action::NEW, Action::EDIT);
        }

        return $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $pdf)
            ->add(Crud::PAGE_DETAIL, $pdf);
    }

    public function createEntity(string $entityFqcn): Facture
    {
        $facture = new Facture();

        $reservationId = (int) $this->requestStack->getCurrentRequest()?->query->get('reservationId');
        $reservation   = $reservationId ? $this->reservationRepo->find($reservationId) : null;

        // La facture est émise par l'entreprise de la réservation, sinon par l'entreprise courante (D-017)
        $facture->setEntreprise($reservation?->getEntreprise() ?? $this->entrepriseCourante->get());

        if ($reservation) {
            $facture->setReservation($reservation);
            $facture->setClientNom($reservation->getGuestPrenom() ?: 'Client');
            $facture->setPrixTtc($reservation->getPrix());
            $facture->setModeReglement($reservation->getModeReglement());
            $facture->setDateTransaction($reservation->getDateHeureDepart() ?? $reservation->getCreatedAt());
        }

        return $facture;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof Facture && !$entityInstance->getNumero()) {
            $entityInstance->setNumero($this->factureRepo->nextNumero(
                $entityInstance->getEntreprise() ?? $this->entrepriseCourante->get(),
                $entityInstance->getDateFacture(),
            ));
        }

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('numero', 'N° facture')->hideOnForm();
        yield DateField::new('dateFacture', 'Date de facturation')->setFormat('dd/MM/yyyy');
        yield DateField::new('dateTransaction', 'Date de la prestation')->setFormat('dd/MM/yyyy');

        yield TextField::new('clientNom', 'Nom du client');
        yield TextareaField::new('clientAdresse', 'Adresse du client')
            ->setNumOfRows(3)->setRequired(false)->hideOnIndex();
        yield TextField::new('clientSiret', 'SIRET du client')
            ->setHelp('Facultatif — pour une facture entreprise')->setRequired(false)->hideOnIndex();
        yield TextField::new('clientTva', 'N° TVA du client')
            ->setRequired(false)->hideOnIndex();

        yield TextField::new('description', 'Description')->hideOnIndex();
        yield NumberField::new('prixTtc', 'Montant TTC (€)')->setNumDecimals(2);
        yield ChoiceField::new('tauxTva', 'Taux de TVA')
            ->setChoices([
                '10 % (transport de personnes)' => 10.0,
                '20 %'                          => 20.0,
                '0 % (exonéré)'                 => 0.0,
            ])
            ->onlyOnForms();
        // Sur la fiche détail : simple texte (la liste de choix en nombres à virgule y plantait, erreur 500)
        yield NumberField::new('tauxTva', 'Taux de TVA')
            ->formatValue(fn($v) => str_replace('.', ',', (string) round((float) $v, 2)) . ' %')
            ->onlyOnDetail();
        yield NumberField::new('montantHt', 'Montant HT (€)')->setNumDecimals(2)->hideOnForm();
        yield NumberField::new('montantTva', 'TVA (€)')->setNumDecimals(2)->hideOnForm();
        yield ChoiceField::new('modeReglement', 'Règlement')
            ->setChoices(['Carte bancaire' => 'carte_bancaire', 'Espèces' => 'especes'])
            ->hideOnIndex();
        yield TextField::new('reservation', 'Réservation liée')
            ->onlyOnDetail()
            ->formatValue(fn($v, Facture $f) => $f->getReservation() ? '#' . $f->getReservation()->getId() : '—');
    }
}
