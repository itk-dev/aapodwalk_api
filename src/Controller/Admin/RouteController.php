<?php

namespace App\Controller\Admin;

use App\Entity\Route;
use App\Field\VichImageField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Validator\Constraints\File;

class RouteController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Route::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('name')
            ->setHelp('Name this');
        yield TextField::new('description');
        yield TextField::new('distance')
            ->setHelp('The distance should be how far the route is with all points of interests included');

        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true)) {
            $entity = $this->getContext()->getEntity()->getInstance();
            assert($entity instanceof Route);
            $refl = new \ReflectionProperty($entity, 'imageFile');
            $attr = [];
            foreach ($refl->getAttributes() as $attribute) {
                if ($attribute->getName() === File::class) {
                    foreach ($attribute->getArguments() as $name => $value) {
                        if ('mimeTypes' === $name) {
                            $attr['accept'] = implode(',', $value);
                        }
                    }
                }
            }

            yield VichImageField::new('imageFile')
                ->onlyOnForms()
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $attr);
        } else {
            yield VichImageField::new('image');
        }

        yield IdField::new('id')->hideOnForm();
        yield AssociationField::new('tags')->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
            ->setHelp('Tags are used in the frontend to organize the routes.');
        yield AssociationField::new('pointsOfInterest')->hideOnIndex()->setRequired(true)
            ->setHelp('Connect points of interest to this podwalk');
        yield DateField::new('createdAt')->hideOnForm();
        yield DateField::new('updatedAt')->hideOnForm();
    }
}
