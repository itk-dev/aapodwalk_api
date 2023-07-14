<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use App\Field\VichFileField;
use App\Field\VichImageField;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Validator\Constraints\File;

class PointOfInterestController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PointOfInterest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name')
            ->setHelp('Name this');
        yield TextField::new('subtitles')->setRequired(true)
            ->setHelp('A text version of the podcast, for people with hearing disabilities.');
        yield TextField::new('latitude')->setRequired(true)
            ->setHelp('The latitude of the interest point');
        yield TextField::new('longitude')->setRequired(true)
            ->setHelp('The longitude of the interest point');

        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true)) {
            $entity = $this->getContext()->getEntity()->getInstance();
            assert($entity instanceof PointOfInterest);

            $imageFileRefl = new \ReflectionProperty($entity, 'imageFile');
            $imageAttr = EasyAdminHelper::getFileInputAttributes($imageFileRefl);
            yield VichImageField::new('imageFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $imageAttr);

            $podcastFileRefl = new \ReflectionProperty($entity, 'podcastFile');
            $podcastAttr = EasyAdminHelper::getFileInputAttributes($podcastFileRefl);
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
    }

}
