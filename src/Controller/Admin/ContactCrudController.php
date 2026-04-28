<?php

namespace App\Controller\Admin;

use App\Entity\Contact;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

class ContactCrudController extends AbstractCrudController
{
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
        return $actions
            ->disable(Action::NEW, Action::EDIT)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        $id        = IdField::new('id', '#')->onlyOnIndex();
        $name      = TextField::new('name', 'Nom');
        $email     = EmailField::new('email', 'Email');
        $phone     = TextField::new('phone', 'Téléphone');
        $message   = TextareaField::new('message', 'Message');
        $createdAt = DateTimeField::new('createdAt', 'Reçu le')
            ->setFormat('dd/MM/yyyy HH:mm');

        if ($pageName === Crud::PAGE_INDEX) {
            return [$id, $name, $email, $phone, $createdAt];
        }

        // Page détail
        return [$name, $email, $phone, $message, $createdAt];
    }
}
