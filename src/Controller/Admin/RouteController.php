<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Route;
use App\Field\VichImageField;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
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

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(new TranslatableMessage('Route', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('Routes', [], 'admin'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', new TranslatableMessage('ID', [], 'admin'))->hideOnForm();
        yield TextField::new('name', new TranslatableMessage('Name', [], 'admin'));
        yield TextField::new('description', new TranslatableMessage('Description', [], 'admin'));
        yield TextField::new('distance', new TranslatableMessage('Distance', [], 'admin'))
            ->setHelp(new TranslatableMessage('The distance should be how far the route is with all points of interests included, e.g. "840m"', [], 'admin'));
        yield TextField::new('centerlatitude', new TranslatableMessage('Center latitude', [], 'admin'))
            ->setHelp(new TranslatableMessage('The latitude for the map to center to, when this route is selected.', [], 'admin'));
        yield TextField::new('centerlongitude', new TranslatableMessage('Center longitude', [], 'admin'))
            ->setHelp(new TranslatableMessage('The longitude for the map to center to, when this route is selected.', [], 'admin'));
        yield TextField::new('zoomValue', new TranslatableMessage('Zoom value', [], 'admin'))
            ->setHelp(new TranslatableMessage('The level of zoom for the map to comfortably show the route (0-28).', [], 'admin'));
        yield TextField::new('partcount', new TranslatableMessage('Part count', [], 'admin'))
            ->setHelp(new TranslatableMessage('The number of points of interest in this route.', [], 'admin'));
        yield TextField::new('totalduration', new TranslatableMessage('Total duration', [], 'admin'))
            ->setHelp(new TranslatableMessage('The total duration of the audio tracks in this route.', [], 'admin'));

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof Route);
            $attr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');

            yield VichImageField::new('imageFile', new TranslatableMessage('Image', [], 'admin'))
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $attr);
        } else {
            yield VichImageField::new('image');
        }

        yield AssociationField::new('tags', new TranslatableMessage('Tags', [], 'admin'))->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
        ->setHelp(new TranslatableMessage('Tags are used in the frontend to organize the routes.', [], 'admin'));
        yield AssociationField::new('pointsOfInterest', new TranslatableMessage('Points', [], 'admin'))->hideOnIndex()->setRequired(true)
            ->setHelp(new TranslatableMessage('Connect points of interest to this podwalk.', [], 'admin'));

        yield DateField::new('createdAt', new TranslatableMessage('Created at', [], 'admin'))->hideOnForm();
        yield DateField::new('updatedAt', new TranslatableMessage('Updated at', [], 'admin'))->hideOnForm();
        $createdBy = AssociationField::new('createdBy', new TranslatableMessage('Created by', [], 'admin'))
            ->setPermission(Role::USER_ADMIN->value);
        if (!$this->isGranted(Role::ADMIN->value)) {
            $createdBy->hideOnForm();
        }
        yield $createdBy;
    }
}
