<?php

namespace App\Controller\Admin;

use App\Admin\Field\LocationField;
use App\Entity\PointOfInterest;
use App\Entity\Role;
use App\Field\VichImageField;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
        yield IdField::new('id', new TranslatableMessage('ID', [], 'admin'))->hideOnForm();
        yield TextField::new('name', new TranslatableMessage('Name', [], 'admin'));

        $mediaUrlLabel = new TranslatableMessage('Media URL', [], 'admin');
        yield UrlField::new('mediaUrl', $mediaUrlLabel);
        yield BooleanField::new('mediaIsAudio', new TranslatableMessage('Is audio?', [], 'admin'))
            ->setHelp(new TranslatableMessage('Check if "{media_url}" points to an audio file.', [
                'media_url' => $mediaUrlLabel,
            ], 'admin'))
            ->renderAsSwitch(false);

        yield TextField::new('subtitles', new TranslatableMessage('Subtitles', [], 'admin'))->setRequired(true)
        ->setHelp(new TranslatableMessage('A text version of the podcast, for people with hearing disabilities.', [], 'admin'));
        yield NumberField::new('poiOrder', new TranslatableMessage('Order', [], 'admin'))->setRequired(false)
        ->setHelp(new TranslatableMessage('The order of the interest point.', [], 'admin'));
        yield LocationField::new('location', new TranslatableMessage('Location', [], 'admin'))
            ->setRequired(true)
            ->setVirtual(true);
        yield NumberField::new('proximityToUnlock', new TranslatableMessage('Proximity to unlock', [], 'admin'))
        ->setHelp(new TranslatableMessage('The proximity that allows unlocking this point of interest (in m).', [], 'admin'));

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof PointOfInterest);

            $imageAttr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');
            yield VichImageField::new('imageFile')
                ->setLabel(new TranslatableMessage('Image', [], 'admin'))
                ->setRequired(true)
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $imageAttr);
        } else {
            yield VichImageField::new('image')
                ->setLabel(new TranslatableMessage('Image', [], 'admin'));
        }

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
