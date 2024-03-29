<?php

namespace App\Controller\Admin;

use App\Entity\Route;
use App\Field\VichImageField;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
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
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('description');
        yield TextField::new('distance')
            ->setHelp(new TranslatableMessage('The distance should be how far the route is with all points of interests included, e.g. "840m"', [], 'admin'));
        yield TextField::new('centerlatitude')
            ->setHelp(new TranslatableMessage('The latitude for the map to center to, when this route is selected.', [], 'admin'));
        yield TextField::new('centerlongitude')
            ->setHelp(new TranslatableMessage('The longitude for the map to center to, when this route is selected.', [], 'admin'));
        yield TextField::new('zoomValue')
            ->setHelp(new TranslatableMessage('The level of zoom for the map to comfortably show the route (0-28).', [], 'admin'));
        yield TextField::new('partcount')
            ->setHelp(new TranslatableMessage('The number of points of interest in this route.', [], 'admin'));
        yield TextField::new('totalduration')
            ->setHelp(new TranslatableMessage('The total duration of the audio tracks in this route.', [], 'admin'));

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof Route);
            $attr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');

            yield VichImageField::new('imageFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $attr);
        } else {
            yield VichImageField::new('image');
        }

        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('tags')->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
        ->setHelp(new TranslatableMessage('Tags are used in the frontend to organize the routes.', [], 'admin'));
        yield AssociationField::new('pointsOfInterest')->hideOnIndex()->setRequired(true)
            ->setHelp(new TranslatableMessage('Connect points of interest to this podwalk.', [], 'admin'));
        yield DateField::new('createdAt')->hideOnForm();
        yield DateField::new('updatedAt')->hideOnForm();
    }
}
