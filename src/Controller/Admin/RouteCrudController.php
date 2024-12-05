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

class RouteCrudController extends AbstractCrudController
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
        yield TextField::new('totalDuration', new TranslatableMessage('Total duration', [], 'admin'))
            ->setHelp(new TranslatableMessage('The total duration of the route, i.e. the total duration of audio tracks in the route plus the time needed for moving along the route.', [], 'admin'));

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof Route);
            $attr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');

            yield VichImageField::new('imageFile')
                ->setLabel(new TranslatableMessage('Image', [], 'admin'))
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
