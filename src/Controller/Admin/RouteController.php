<?php

namespace App\Controller\Admin;

use App\Entity\Route;
use App\Field\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RouteController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Route::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')
                ->setHelp('Name this'),
            TextField::new('description'),
            TextField::new('distance')
                ->setHelp('The distance should be how far the route is with all points of interests included'),
            TextField::new('image')
                ->onlyOnIndex(),
            VichImageField::new('imageFile')
                ->setFormTypeOption('allow_delete', false)
                ->onlyOnForms(),

            IdField::new('id')->hideOnForm(),
            AssociationField::new('tags')->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
                ->setHelp('Tags are used in the frontend to organize the routes.'),
            AssociationField::new('pointsOfInterest')->hideOnIndex()->setRequired(true)
                ->setHelp('Connect points of interest to this podwalk'),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
