<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController as BaseAbstractCrudController;

abstract class AbstractCrudController extends BaseAbstractCrudController
{
    public function configureCrud(Crud $crud): Crud
    {
        return parent::configureCrud($crud)
            ->showEntityActionsInlined()
            ->setAutofocusSearch()
            // https://symfony.com/bundles/EasyAdminBundle/current/design.html#form-field-templates
            // Our form theme extends EaseAdmin's form theme.
            ->setFormThemes(['admin/form.html.twig'])
            ->overrideTemplate('layout', 'admin/layout.html.twig');
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            // Deleting stuff is a mess.
            ->disable(Action::DELETE);
    }
}
