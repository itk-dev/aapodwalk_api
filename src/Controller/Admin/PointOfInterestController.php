<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use App\Field\VichFileField;
use App\Field\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use Symfony\Component\Translation\TranslatableMessage;

class PointOfInterestController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return PointOfInterest::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')->setRequired(true)
                ->setHelp('Name this'),
            TextField::new('latitude')->setRequired(true)
                ->setHelp('The latitude of the interest point'),
            TextField::new('longitude')->setRequired(true)
                ->setHelp('The longitude of the interest point'),
            TextField::new('subtitles')->setRequired(true)
                ->setHelp('A text version of the podcast, for people with hearing disabilities.'),

            TextField::new('image')
                ->onlyOnIndex(),
            VichImageField::new('imageFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false),

            TextField::new('podcast')
                ->onlyOnIndex(),
            VichFileField::new('podcastFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false),

            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
