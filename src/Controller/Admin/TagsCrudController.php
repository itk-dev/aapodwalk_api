<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Tag;
use App\Repository\TagRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class TagsCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly TagRepository $tagsRepository,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Tag::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(new TranslatableMessage('Tag', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('Tags', [], 'admin'));
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', new TranslatableMessage('ID', [], 'admin'))->hideOnForm();
        yield TextField::new('name', new TranslatableMessage('Name', [], 'admin'))
            ->setHelp(new TranslatableMessage('The name of the tag', [], 'admin'));

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
