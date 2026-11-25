<?php

namespace App\Controller\Admin;

use App\Entity\Entreprise;
use App\Enum\EtatAbonne;
use App\Enum\TypeAbonnement;
use App\Repository\CompteAbonneRepository;
use App\Repository\SocieteRepository;
use App\Service\EntrepriseCourante;
use App\Service\GrilleAbonnes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Menu « Abonnés » du super-admin (D-074 à D-078) : liste des entreprises abonnées,
 * fiche (compte + société saisie) et bouton « Valider » qui lance le mois d'essai (D-077).
 * SIMPLICI CAB (entreprise n°1) n'apparaît jamais ici et ne peut pas être modifiée par cet écran.
 */
#[IsGranted('ROLE_SUPER_ADMIN')]
class AbonneCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly SocieteRepository $societeRepo,
        private readonly CompteAbonneRepository $compteRepo,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly GrilleAbonnes $grilleAbonnes,
    ) {}

    public static function getEntityFqcn(): string
    {
        return Entreprise::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Abonné')
            ->setEntityLabelInPlural('Abonnés')
            ->setPageTitle(Crud::PAGE_INDEX, 'Abonnés')
            ->setPageTitle(Crud::PAGE_EDIT, fn (Entreprise $e) => 'Modifier l\'abonné ' . $e->getNom())
            ->setHelp(Crud::PAGE_EDIT, 'Pour valider une société en attente, utilisez plutôt le bouton « Fiche et validation » : il lance aussi le mois d\'essai.')
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $fiche = Action::new('fiche', 'Fiche et validation', 'fa fa-id-card')->linkToCrudAction('fiche');

        return $actions
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $fiche)
            ->add(Crud::PAGE_EDIT, $fiche);
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('nom', 'Nom de l\'activité');
        yield ChoiceField::new('typeAbonnement', 'Abonnement')
            ->setChoices(TypeAbonnement::choix())
            ->formatValue(fn ($v) => $v instanceof TypeAbonnement ? $v->label() : '—');
        yield ChoiceField::new('etat', 'État')
            ->setChoices(EtatAbonne::choix())
            ->formatValue(fn ($v) => $v instanceof EtatAbonne ? $v->label() : '')
            ->setHelp('D-014 : « Lecture seule » en cas d\'impayé.');
        yield DateField::new('essaiDebut', 'Début d\'essai')->setFormat('dd/MM/yyyy')->setRequired(false);
        yield DateField::new('essaiFin', 'Fin d\'essai')->setFormat('dd/MM/yyyy')->hideOnForm()->setSortable(false);
        yield DateTimeField::new('createdAt', 'Inscrit le')->setFormat('dd/MM/yyyy HH:mm')->hideOnForm();
    }

    /** Jamais SIMPLICI CAB dans la liste. */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->andWhere('entity.id <> :principale')
            ->setParameter('principale', EntrepriseCourante::ID_PRINCIPALE);
    }

    public function edit(AdminContext $context)
    {
        $this->abonneOu404($context);

        return parent::edit($context);
    }

    #[AdminAction(routePath: '/{entityId}/fiche', routeName: 'fiche', methods: ['GET', 'POST'])]
    public function fiche(AdminContext $context, Request $request, EntityManagerInterface $em): Response
    {
        $entreprise = $this->abonneOu404($context);

        // La société d'un abonné est cachée par le tri automatique (entreprise ≠ SIMPLICI CAB) :
        // on lève le tri le temps de la lecture, réservé au super-admin.
        $societe = $this->entrepriseCourante->sansCloisonnement(
            fn () => $this->societeRepo->trouverPourEntreprise($entreprise)
        );

        $url = $this->adminUrlGenerator->setController(self::class)->setAction('fiche')->setEntityId($entreprise->getId())->generateUrl();

        if ($request->isMethod('POST')) {
            if (!$this->isCsrfTokenValid('valider-abonne-' . $entreprise->getId(), (string) $request->request->get('_token'))) {
                $this->addFlash('danger', 'Le formulaire a expiré, réessayez.');
            } elseif (!$entreprise->isEnAttente()) {
                $this->addFlash('warning', 'Cet abonné est déjà validé.');
            } elseif (!$societe) {
                $this->addFlash('warning', 'L\'abonné n\'a pas encore rempli « Ma société ».');
            } else {
                $entreprise->valider();
                $em->flush();
                $this->grilleAbonnes->creerPour($entreprise); // D-087 : sa grille, copiée de vos prix
                $this->addFlash('success', sprintf('%s est validé : essai gratuit jusqu\'au %s.', $entreprise->getNom(), $entreprise->getEssaiFin()->format('d/m/Y')));
            }

            return $this->redirect($url);
        }

        return $this->render('admin/abonne/fiche.html.twig', [
            'entreprise' => $entreprise,
            'societe' => $societe,
            'compte' => $this->compteRepo->pourEntreprise($entreprise),
            'urlListe' => $this->adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->unset('entityId')->generateUrl(),
            'urlModifier' => $this->adminUrlGenerator->setController(self::class)->setAction(Action::EDIT)->setEntityId($entreprise->getId())->generateUrl(),
        ]);
    }

    private function abonneOu404(AdminContext $context): Entreprise
    {
        $entreprise = $context->getEntity()->getInstance();
        if (!$entreprise instanceof Entreprise || $entreprise->getId() === EntrepriseCourante::ID_PRINCIPALE) {
            throw $this->createNotFoundException();
        }

        return $entreprise;
    }
}
