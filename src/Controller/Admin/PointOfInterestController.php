<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use App\Entity\Role;
use App\Field\VichFileField;
use App\Field\VichImageField;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class PointOfInterestController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PointOfInterest::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle('index', new TranslatableMessage('Point of interest title', [], 'messages'));
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setIcon('fa fa-plus')->setLabel(new TranslatableMessage('Add a new point of interest', [], 'messages'));
            });
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name');
        yield TextField::new('subtitles')->setRequired(true)
        ->setHelp(new TranslatableMessage('A text version of the podcast, for people with hearing disabilities.', [], 'messages'));
        yield NumberField::new('poiOrder')->setRequired(false)
        ->setHelp(new TranslatableMessage('The order of the interest point.', [], 'admin'));
        yield TextField::new('latitude')->setRequired(true)
        ->setHelp(new TranslatableMessage('The latitude of the interest point.', [], 'admin'));
        yield TextField::new('longitude')->setRequired(true)
        ->setHelp(new TranslatableMessage('The longitude of the interest point.', [], 'admin'));
        yield NumberField::new('proximityToUnlock')
        ->setHelp(new TranslatableMessage('The proximity that allows unlocking this point of interest (in m).', [], 'admin'));

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof PointOfInterest);

            $imageAttr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');
            yield VichImageField::new('imageFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $imageAttr);

            $podcastAttr = EasyAdminHelper::getFileInputAttributes($entity, 'podcastFile');
            yield VichFileField::new('podcastFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $podcastAttr);
        } else {
            yield VichImageField::new('image');
            yield VichFileField::new('podcast');
        }

        yield DateField::new('createdAt')->hideOnForm();
        yield DateField::new('updatedAt')->hideOnForm();
        $createdBy = AssociationField::new('createdBy', new TranslatableMessage('Created by'))
            ->setPermission(Role::USER_ADMIN->value);
        if (!$this->isGranted(Role::ADMIN->value)) {
            $createdBy->hideOnForm();
        }
        yield $createdBy;
    }
}
