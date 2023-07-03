<?php

namespace App\Controller\Admin;

use App\Entity\ApiUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

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
            TextField::new('name')->setRequired(true)
                ->setHelp('User friendly name for this API user.'),
            TextField::new('token')->setRequired(true)
                ->setHelp('Access token used by the screen to access configuration and tiles.'),
            TextField::new('remoteApiKey')->setRequired(true)
                ->setHelp('Remote API key from "selvbetjening" to access user submissions.'),
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
