<?php

namespace App\Controller\Espace;

use App\Controller\Traits\GrilleTarifChampsTrait;
use App\Entity\GrilleTarif;
use App\Enum\EtatAbonne;
use App\Service\EntrepriseCourante;
use App\Service\GrilleAbonnes;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminAction;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * « Mes prix » de l'abonné (D-093) : le même écran que « Grille de prix » de l'admin,
 * limité à SES lignes, sans ajout ni suppression, avec « Prix ZenCAR / Mes prix » (D-091).
 * Accessible seulement depuis le tableau de bord /espace (allowedControllers), jamais depuis /admin.
 */
#[IsGranted('ROLE_ABONNE')]
#[AdminCrud(routePath: '/mes-prix', routeName: 'mes_prix')]
class MesPrixCrudController extends AbstractCrudController
{
    use GrilleTarifChampsTrait;

    public function __construct(
        private readonly EntrepriseCourante $entrepriseCourante,
        private readonly GrilleAbonnes $grilleAbonnes,
        private readonly AdminUrlGenerator $adminUrlGenerator,
        private readonly CsrfTokenManagerInterface $csrf,
    ) {}

    public static function getEntityFqcn(): string
    {
        return GrilleTarif::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Prix d\'un véhicule')
            ->setEntityLabelInPlural('Mes prix')
            ->setPageTitle(Crud::PAGE_INDEX, 'Mes prix')
            ->setHelp(Crud::PAGE_INDEX, 'Vos prix par véhicule. « Prix ZenCAR » : ils suivent les mises à jour de ZenCAR. '
                . 'Dès que vous modifiez un chiffre d\'un véhicule, il passe en « Mes prix » et garde vos chiffres. Cochez « Proposé » pour les véhicules que vous avez.')
            ->setHelp(Crud::PAGE_EDIT, 'Modifier un chiffre fait passer ce véhicule en « Mes prix » : il ne suivra plus les prix ZenCAR.')
            ->setDefaultSort(['id' => 'ASC'])
            ->setSearchFields(null);
    }

    public function configureActions(Actions $actions): Actions
    {
        $tokenId = 'raccrocher';
        $raccrocher = Action::new('raccrocher', 'Revenir aux prix ZenCAR', 'fa fa-undo')
            ->linkToUrl(fn (GrilleTarif $g) => $this->adminUrlGenerator->setController(self::class)->setAction('raccrocher')
                ->setEntityId($g->getId())->set('_token', $this->csrf->getToken($tokenId)->getValue())->generateUrl())
            ->displayIf(fn (GrilleTarif $g) => !$g->isSuitZencar() && $this->modifiable());

        $actions = $actions
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $raccrocher)
            ->add(Crud::PAGE_DETAIL, $raccrocher)
            ->disable(Action::NEW, Action::DELETE, Action::BATCH_DELETE);

        if (!$this->modifiable()) {
            $actions->disable(Action::EDIT);
        }

        return $actions;
    }

    public function configureAssets(Assets $assets): Assets
    {
        return $this->assetsGrille($assets); // prix au seuil et exemple en direct (D-090)
    }

    public function configureFields(string $pageName): iterable
    {
        yield from $this->champsGrille($pageName);

        // D-091 : « Prix ZenCAR » (suit vos mises à jour) ou « Mes prix »
        yield BooleanField::new('suitZencar', 'Prix ZenCAR')
            ->renderAsSwitch(false)
            ->hideOnForm()
            ->setHelp('Oui : suit les prix ZenCAR. Non : vos propres prix.');
    }

    /** Seulement les lignes de l'abonné connecté (en plus du tri automatique par entreprise). */
    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, FilterCollection $filters): QueryBuilder
    {
        return parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters)
            ->join('entity.categorie', 'c')
            ->andWhere('entity.entreprise = :e')->setParameter('e', $this->entrepriseCourante->get())
            ->andWhere('c.isActive = true');
    }

    public function index(AdminContext $context)
    {
        if ($this->entrepriseCourante->get()->isEnAttente()) {
            return $this->redirectToRoute('espace_accueil');
        }
        $this->grilleAbonnes->creerPour($this->entrepriseCourante->get()); // filet : lignes manquantes

        return parent::index($context);
    }

    public function detail(AdminContext $context)
    {
        $this->sienneOu404($context);

        return parent::detail($context);
    }

    public function edit(AdminContext $context)
    {
        $this->sienneOu404($context);
        if (!$this->modifiable()) {
            throw $this->createAccessDeniedException();
        }

        return parent::edit($context);
    }

    /** Un chiffre changé = l'abonné fixe ses propres prix : ce véhicule ne suit plus ZenCAR (D-091). */
    public function updateEntity(EntityManagerInterface $em, $grille): void
    {
        if ($grille instanceof GrilleTarif) {
            $avant = $em->getUnitOfWork()->getOriginalEntityData($grille);
            $cles = ['thresholdKm', 'basePriceUnderThreshold', 'pricePerKmUnderThreshold', 'pricePerKmOverThreshold'];
            $original = array_map(fn ($k) => round((float) ($avant[$k] ?? 0), 2), $cles);
            if ($original !== array_map(fn ($x) => round($x, 2), $grille->prix())) {
                $grille->setSuitZencar(false);
            }
            $grille->setUpdatedAt(new \DateTimeImmutable());
        }
        parent::updateEntity($em, $grille);
    }

    #[AdminAction(routePath: '/{entityId}/raccrocher', routeName: 'raccrocher', methods: ['GET'])]
    public function raccrocher(AdminContext $context, Request $request): Response
    {
        $grille = $this->sienneOu404($context);
        if ($this->modifiable() && $this->isCsrfTokenValid('raccrocher', (string) $request->query->get('_token'))) {
            $this->grilleAbonnes->raccrocher($grille);
            $this->addFlash('success', sprintf('%s : prix ZenCAR remis.', $grille->getCategorie()->getLabel()));
        }

        return $this->redirect($this->adminUrlGenerator->setController(self::class)->setAction(Action::INDEX)->unset('entityId')->unset('_token')->generateUrl());
    }

    private function sienneOu404(AdminContext $context): GrilleTarif
    {
        $g = $context->getEntity()->getInstance();
        if (!$g instanceof GrilleTarif || $g->getEntreprise()?->getId() !== $this->entrepriseCourante->get()->getId()) {
            throw $this->createNotFoundException();
        }

        return $g;
    }

    private function modifiable(): bool
    {
        $e = $this->entrepriseCourante->get();

        return !$e->isEnAttente() && $e->getEtat() !== EtatAbonne::LECTURE_SEULE;
    }
}
