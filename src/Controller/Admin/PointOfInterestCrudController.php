<?php

namespace App\Controller\Admin;

use App\Admin\Field\LocationField;
use App\Admin\Field\ValueWithUnitField;
use App\Entity\PointOfInterest;
use App\Entity\Role;
use App\Entity\Route;
use App\Field\VichImageField;
use App\Form\ValueWithUnitType;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use Symfony\Component\Translation\TranslatableMessage;

class PointOfInterestCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PointOfInterest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(new TranslatableMessage('Point of interest', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('Points of interest', [], 'admin'));
    }

    public function configureFields(string $pageName): iterable
    {
        $route = AssociationField::new('route', new TranslatableMessage('Route', [], 'admin'))
            ->setRequired(true);

        $position = NumberField::new('poiOrder', new TranslatableMessage('Order', [], 'admin'))
            ->addCssClass('route-point-position')
            ->setRequired(false);

        // Check if this controller is being used in a collection.
        if ($this->getContext()?->getEntity()?->getInstance() instanceof Route) {
            $route->addCssClass('visually-hidden');
            $position->addCssClass('visually-hidden');
        }

        yield $route;
        yield $position;

        yield IdField::new('id', new TranslatableMessage('ID', [], 'admin'))->hideOnForm();
        yield TextField::new('name', new TranslatableMessage('Name', [], 'admin'))->setColumns(12);

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            // @todo This assertion is not true since entity is a Route (cf. https://github.com/EasyCorp/EasyAdminBundle/issues/3424).
            assert($entity instanceof PointOfInterest);

            $imageAttr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');
            yield VichImageField::new('imageFile')
                // @todo Replace the hack assets/admin.js.
                // ->setRequired(null === $entity->getImage())
                ->setLabel(new TranslatableMessage('Image', [], 'admin'))
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $imageAttr)->setColumns(12);
        } else {
            yield VichImageField::new('image')
                ->setLabel(new TranslatableMessage('Image', [], 'admin'))->setColumns(12);
        }

        $mediaUrlLabel = new TranslatableMessage('Media URL', [], 'admin');
        yield UrlField::new('mediaUrl', $mediaUrlLabel)
            ->setFormTypeOptions([
                'block_name' => 'mediaUrl',
            ])->setColumns(12);
        yield BooleanField::new('mediaIsAudio', new TranslatableMessage('Is audio?', [], 'admin'))
            ->setHelp(new TranslatableMessage('Check if "{media_url}" points to an audio file.', [
                'media_url' => $mediaUrlLabel,
            ], 'admin'))
            ->renderAsSwitch(false)->setColumns(12);

        yield TextareaField::new('subtitles', new TranslatableMessage('Subtitles', [], 'admin'))
            ->setRequired(true)
            ->setHelp(new TranslatableMessage('A text version of the podcast, for people with hearing disabilities.', [], 'admin'))->setColumns(12);

        yield LocationField::new('location', new TranslatableMessage('Location', [], 'admin'))
            ->setRequired(true)
            ->setVirtual(true)->setColumns(12);

        yield ValueWithUnitField::new('proximityToUnlock', new TranslatableMessage('Proximity to unlock', [], 'admin'))
            ->setFormTypeOption('units', [
                'm' => [
                    ValueWithUnitType::OPTION_LABEL => new TranslatableMessage('meter', [], 'admin'),
                    ValueWithUnitType::OPTION_SCALE => 1,
                    ValueWithUnitType::OPTION_LOCALIZED_UNIT => new TranslatableMessage('unit.m', [], 'admin'),
                ],
            ])
            ->setHelp(new TranslatableMessage('The proximity that allows unlocking this point of interest.', [], 'admin'))->setColumns(12);

        yield DateField::new('createdAt', new TranslatableMessage('Created at', [], 'admin'))->hideOnForm();
        yield DateField::new('updatedAt', new TranslatableMessage('Updated at', [], 'admin'))->hideOnForm();
        $createdBy = AssociationField::new('createdBy', new TranslatableMessage('Created by', [], 'admin'))
            ->setPermission(Role::USER_ADMIN->value)->setColumns(12);
        if (!$this->isGranted(Role::ADMIN->value)) {
            $createdBy->hideOnForm();
        }
        yield $createdBy;
    }
}
