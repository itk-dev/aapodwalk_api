<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class TagsController extends AbstractCrudController
{
    public function __construct(
        private readonly TagsRepository $tagsRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Tags::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name')
            ->setHelp(new TranslatableMessage('The name of the tag', [], 'admin')),
        ];
    }
}
