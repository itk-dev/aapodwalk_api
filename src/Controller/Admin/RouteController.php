<?php

namespace App\Controller\Admin;

use App\Entity\Route;
use App\Validator\Constraints\EasyAdminFile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

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
            ->setHelp(new TranslatableMessage('The name of the route', [], 'admin')),
            TextField::new('description'),
            TextField::new('distance')
            ->setHelp(new TranslatableMessage('The distance should be how far the route is with all points of interests included, e.g. "840m"', [], 'admin')),
            ImageField::new('image')
             ->setUploadDir('public/')
             ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOption(
                'constraints',
                [
                    new EasyAdminFile([
                        'mimeTypes' => [ // We want to let upload only jpeg or png
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ]
            ),
            IdField::new('id')->hideOnForm(),
            AssociationField::new('tags')->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
                ->setHelp(new TranslatableMessage('Tags are used in the frontend to organize the routes. If the route is not connected to a tag, it will not be displayed in the frotnend', [], 'admin')),
            AssociationField::new('pointsOfInterest')->hideOnIndex()->setRequired(true)
            ->setHelp(new TranslatableMessage('Connect points of interest to this podwalk', [], 'admin')),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
