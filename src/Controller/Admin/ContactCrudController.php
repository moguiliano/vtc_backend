<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\HttpFoundation\Response;

class ContactCrudController extends AbstractCrudController
{
    public function __construct(private EntityManagerInterface $em) {}

    public static function getEntityFqcn(): string
    {
        return Contact::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Message')
            ->setEntityLabelInPlural('Messages de contact')
            ->setPageTitle(Crud::PAGE_INDEX, 'Messages reçus')
            ->setDefaultSort(['createdAt' => 'DESC'])
            ->overrideTemplate('crud/index', 'admin/contact/index.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        $markUnread = Action::new('markUnread', 'Marquer non lu', 'fa fa-envelope')
            ->linkToCrudAction('markAsUnread')
            ->displayIf(fn(Contact $c) => $c->isRead());

        $markRead = Action::new('markRead', 'Marquer lu', 'fa fa-envelope-open')
            ->linkToCrudAction('markAsRead')
            ->displayIf(fn(Contact $c) => !$c->isRead());

        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_DETAIL, $markUnread)
            ->add(Crud::PAGE_DETAIL, $markRead)
            ->add(Crud::PAGE_INDEX, $markRead)
            ->add(Crud::PAGE_INDEX, $markUnread);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(BooleanFilter::new('isRead', 'Lu'));
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IdField::new('id', '#')->onlyOnIndex();

        $name = TextField::new('name', 'Nom');
        $email = EmailField::new('email', 'Email');
        $phone = TextField::new('phone', 'Téléphone');
        $message = TextareaField::new('message', 'Message')
            ->setNumOfRows(6)
            ->renderAsHtml();

        $isRead = BooleanField::new('isRead', 'Lu')
            ->renderAsSwitch(false);

        $createdAt = DateTimeField::new('createdAt', 'Reçu le')
            ->setFormat('dd/MM/yyyy HH:mm');

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $isRead, $name, $email, $phone, $createdAt];
        }

        return [$name, $email, $phone, $message, $isRead, $createdAt];
    }

    // Marquage automatique comme lu à l'ouverture du détail
    public function detail(AdminContext $context): Response
    {
        $response = parent::detail($context);

        $contact = $context->getEntity()->getInstance();
        if ($contact instanceof Contact && !$contact->isRead()) {
            $contact->setIsRead(true);
            $this->em->flush();
        }

        return $response;
    }

    public function markAsRead(AdminContext $context): Response
    {
        $contact = $context->getEntity()->getInstance();
        if ($contact instanceof Contact) {
            $contact->setIsRead(true);
            $this->em->flush();
        }
        return $this->redirect($context->getReferrer() ?? $this->generateUrl('admin'));
    }

    public function markAsUnread(AdminContext $context): Response
    {
        $contact = $context->getEntity()->getInstance();
        if ($contact instanceof Contact) {
            $contact->setIsRead(false);
            $this->em->flush();
        }
        return $this->redirect($context->getReferrer() ?? $this->generateUrl('admin'));
    }
}
