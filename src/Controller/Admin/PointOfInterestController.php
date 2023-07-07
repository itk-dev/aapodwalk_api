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
                ->setHelp(new TranslatableMessage('The name of the point of interest', [], 'admin')),
            TextField::new('latitude')->setRequired(true)
                ->setHelp(new TranslatableMessage('The latitude of the interest point', [], 'admin')),
            TextField::new('longitude')->setRequired(true)
            ->setHelp(new TranslatableMessage('The longitude of the interest point', [], 'admin')),
            TextField::new('subtitles')->setRequired(true)
            ->setHelp(new TranslatableMessage('A text version of the podcast, for people with hearing disabilities.', [], 'admin')),
            ImageField::new('image')->setRequired(true)->setUploadDir('/public/points-of-interest')->hideWhenUpdating(),
            TextField::new('podcast')->setRequired(true)->setFormType(FileUploadType::class)->addJsFiles(Asset::fromEasyAdminAssetPackage('field-file-upload.js'))->hideWhenUpdating(),
            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
