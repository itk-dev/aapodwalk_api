<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;

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
            TextField::new('name')
                ->setHelp('Name this'),
            TextField::new('latitude')
                ->setHelp('The latitude of the interest point'),
            TextField::new('longitude')
                ->setHelp('The longitude of the interest point'),
            TextField::new('subtitles')
                ->setHelp('A text version of the podcast, for people with hearing disabilities.'),
            ImageField::new('image')->setUploadDir('/public/points-of-interest'),
            TextField::new('podcast')->setFormType(FileUploadType::class)->addJsFiles(Asset::fromEasyAdminAssetPackage('field-file-upload.js')),
            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
