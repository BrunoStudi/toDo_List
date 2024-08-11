<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions 
    {
        return $actions
        ->add(Crud::PAGE_EDIT, Action::INDEX)
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')
                ->hideOnForm()
                ->hideOnIndex(),
            ImageField::new('avatar')
            -> setFormTypeOptions([
                "multiple" => false,
                'attr' => [
                    'accept' => 'image/x-png, image/gif, image/jpeg, image/jpg, image/webp'
                ]
            ])
            -> setBasePath("uploads/images/")
            -> setUploadDir("/public/uploads/images/")
            -> setUploadedFileNamePattern('[randomhash].[extension]')
            -> setRequired($pageName === Crud::PAGE_NEW),
            TextField::new('pseudo'),
            TextField::new('name'),
            TextField::new('surname'),
            EmailField::new('email'),
            ArrayField::new('roles')
        ];
    }
}
