<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Tags;
use App\Repository\TagsRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
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
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name')
            ->setHelp(new TranslatableMessage('The name of the tag', [], 'admin'));
        $createdBy = AssociationField::new('createdBy', new TranslatableMessage('Created by', [], 'admin'))
            ->setPermission(Role::USER_ADMIN->value);
        if (!$this->isGranted(Role::ADMIN->value)) {
            $createdBy->hideOnForm();
        }
        yield $createdBy;
    }
}
