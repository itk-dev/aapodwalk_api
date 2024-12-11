<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\Route;
use App\Field\VichImageField;
use App\Service\AppManager;
use App\Service\EasyAdminHelper;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Translation\TranslatableMessage;

class RouteCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly AppManager $appManager,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return Route::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->setEntityLabelInSingular(new TranslatableMessage('Route', [], 'admin'))
            ->setEntityLabelInPlural(new TranslatableMessage('Routes', [], 'admin'));
    }

    public function configureActions(Actions $actions): Actions
    {
        $actions = parent::configureActions($actions);

        $apps = $this->appManager->getApps();
        foreach ($apps as $app) {
            $actions
                ->add(Crud::PAGE_INDEX, Action::new('show_in_app_'.$app->getId())
                    ->setLabel(new TranslatableMessage('Preview app {name}', ['name' => $app->getName()], 'admin'))
                    ->linkToUrl(fn (Route $route) => $this->generateUrl('app_show', [
                        'id' => $app->getId(),
                        'path' => $app->getPath('route', ['id' => $route->getId()], absolute: false),
                    ])));
        }

        return $actions;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id', new TranslatableMessage('ID', [], 'admin'))->hideOnForm();
        yield TextField::new('name', new TranslatableMessage('Name', [], 'admin'))->setColumns(12);
        yield TextareaField::new('description', new TranslatableMessage('Description', [], 'admin'))->setColumns(6);

        $context = $this->getContext();
        if (in_array($pageName, [Crud::PAGE_NEW, Crud::PAGE_EDIT], true) && null !== $context) {
            $entity = $context->getEntity()->getInstance();
            assert($entity instanceof Route);
            $attr = EasyAdminHelper::getFileInputAttributes($entity, 'imageFile');

            yield VichImageField::new('imageFile')
                ->setRequired(null === $entity->getImage())
                ->setLabel(new TranslatableMessage('Image', [], 'admin'))
                ->setFormTypeOption('allow_delete', false)
                ->setFormTypeOption('attr', $attr)
                ->setColumns(6);
        } else {
            yield VichImageField::new('image')->setColumns(6);
        }

        yield TextField::new('distance', new TranslatableMessage('Distance', [], 'admin'))->setColumns(6)
        ->setHelp(new TranslatableMessage('The distance should be how far the route is with all points of interests included, e.g. "840m"', [], 'admin'));
        yield TextField::new('totalDuration', new TranslatableMessage('Total duration', [], 'admin'))->setColumns(6)
        ->setHelp(new TranslatableMessage('The total duration of the route, i.e. the total duration of audio tracks in the route plus the time needed for moving along the route.', [], 'admin'));

        yield CollectionField::new('points', new TranslatableMessage('Points', [], 'admin'))->addCssClass('field-collection')
            ->setEntryIsComplex()
            ->useEntryCrudForm(PointOfInterestCrudController::class)
            ->setRequired(true)
            ->setFormTypeOption('attr', [
                'add_new_item_label' => new TranslatableMessage('Add new point', [], 'admin'),
            ])
            // We need some space for the nested forms.
            ->setColumns(12)
        ;

        yield AssociationField::new('tags', new TranslatableMessage('Tags', [], 'admin'))->hideOnIndex()->setRequired(true)->setFormTypeOption('by_reference', false)
        ->setHelp(new TranslatableMessage('Tags are used in the frontend to organize the routes.', [], 'admin'))->setColumns(6);

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
