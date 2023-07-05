<?php

namespace App\Controller\Admin;

use App\Entity\Tags;
use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Form\Type\FileUploadType;
use App\Repository\TagsRepository;
use Symfony\Component\HttpFoundation\Request;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;


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
                ->setHelp('Name this'),
        ];
    }

    public function __invoke(Request $request)
    {
        return $this->tagsRepository->getCollection();
    }
}
