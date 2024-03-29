<?php

namespace App\Controller\Admin;

use App\Entity\ApiUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class ApiUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ApiUser::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true),
            TextField::new('token')->setRequired(true)->setFormTypeOption('disabled', 'disabled')
            ->setHelp(new TranslatableMessage('Access token used by the frontend for access to the api', [], 'admin')),
            DateField::new('createdAt')->hideOnForm()->hideOnIndex(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }

    public function createEntity(string $entityFqcn): ApiUser
    {
        // Generate default token for new users.
        $token = openssl_random_pseudo_bytes(12);
        $token = bin2hex($token);

        $user = new ApiUser();
        $user->setToken($token);

        return $user;
    }
}
