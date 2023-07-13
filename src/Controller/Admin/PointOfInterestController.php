<?php

namespace App\Controller\Admin;

use App\Entity\PointOfInterest;
use App\Validator\Constraints\EasyAdminFile;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
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
            ImageField::new('image')
             ->setUploadDir('public/')
             ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
            ->setFormTypeOption(
                'constraints',
                [
                    new EasyAdminFile([
                        'mimeTypes' => [ // We want to let upload only jpeg or png
                            'image/jpeg',
                            'image/png',
                        ],
                    ]),
                ]
            ),
            ImageField::new('podcast')
            ->setUploadDir('public/')
            ->setUploadedFileNamePattern('[slug]-[timestamp].[extension]')
           ->setFormTypeOption(
               'constraints',
               [
                   new EasyAdminFile([
                       'mimeTypes' => [
                           'audio/mpeg',
                       ],
                   ]),
               ]
           ),
            IdField::new('id')->hideOnForm(),
            DateField::new('createdAt')->hideOnForm(),
            DateField::new('updatedAt')->hideOnForm(),
        ];
    }
}
