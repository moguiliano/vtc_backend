<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\BooleanFilter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class ContactCrudController extends AbstractCrudController
{
    public function __construct(
        private EntityManagerInterface $em,
        private ContactRepository $contactRepo,
    ) {}

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
            ->setDefaultSort(['createdAt' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $markRead = Action::new('markRead', 'Marquer lu', 'fa fa-envelope-open')
            ->linkToUrl(fn(Contact $c) => '/admin/contact/toggle-read/' . $c->getId() . '/1')
            ->displayIf(fn(Contact $c) => !$c->isRead());

        $markUnread = Action::new('markUnread', 'Marquer non lu', 'fa fa-envelope')
            ->linkToUrl(fn(Contact $c) => '/admin/contact/toggle-read/' . $c->getId() . '/0')
            ->displayIf(fn(Contact $c) => $c->isRead());

        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL)
            ->add(Crud::PAGE_INDEX, $markRead)
            ->add(Crud::PAGE_INDEX, $markUnread)
            ->add(Crud::PAGE_DETAIL, $markRead)
            ->add(Crud::PAGE_DETAIL, $markUnread);
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters->add(BooleanFilter::new('isRead', 'Lu'));
    }

    public function configureFields(string $pageName): iterable
    {
        $id        = IdField::new('id', '#')->onlyOnIndex();
        $isRead    = BooleanField::new('isRead', 'Lu')->renderAsSwitch(false);
        $name      = TextField::new('name', 'Nom');
        $email     = EmailField::new('email', 'Email');
        $phone     = TextField::new('phone', 'Téléphone');
        $message   = TextareaField::new('message', 'Message')->setNumOfRows(6);
        $createdAt = DateTimeField::new('createdAt', 'Reçu le')->setFormat('dd/MM/yyyy HH:mm');

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $isRead, $name, $email, $phone, $createdAt];
        }

        return [$name, $email, $phone, $message, $isRead, $createdAt];
    }

    #[Route('/admin/contact/toggle-read/{id}/{value}', name: 'admin_contact_toggle_read')]
    public function toggleRead(int $id, int $value): RedirectResponse
    {
        $contact = $this->contactRepo->find($id);
        if ($contact) {
            $contact->setIsRead((bool) $value);
            $this->em->flush();
        }

        return $this->redirectToRoute('admin', [
            'crudController' => self::class,
            'crudAction'     => 'index',
        ]);
    }
}
