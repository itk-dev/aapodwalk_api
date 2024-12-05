<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\KeyValueStore;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Translation\TranslatableMessage;

class UserCrudController extends AbstractCrudController
{
    public function __construct(
        public UserPasswordHasherInterface $userPasswordHasher,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return parent::configureActions($actions)
            ->setPermission(Action::NEW, Role::ADMIN->value)
            ->setPermission(Action::EDIT, Role::ADMIN->value)
            ->setPermission(Action::DELETE, Role::ADMIN->value)
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        yield TextField::new('email')
            ->setHelp(new TranslatableMessage('Users mail address, which is also used as login name', [], 'admin'));
        yield TextField::new('password')
            ->setFormType(RepeatedType::class)
            ->setFormTypeOptions([
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => '(Repeat)'],
                'mapped' => false,
            ])
            ->setRequired(Crud::PAGE_NEW === $pageName)
            ->onlyWhenCreating();

        $options = array_combine(
            Role::values(),
            Role::values()
        );
        yield ChoiceField::new('roles', new TranslatableMessage('Roles', [], 'admin'))
            ->setTemplatePath('admin/User/roles.html.twig')
            ->setFormTypeOptions([
                'multiple' => true,
                'expanded' => true,
                'choices' => $options,
            ])
        ;

        if ($this->isGranted(Role::USER_ADMIN->value)) {
            yield TextField::new('apiToken')
                ->setHelp(new TranslatableMessage('The user must also have the {api_role} to access the API.', [
                    'api_role' => Role::API->value,
                ], 'admin'))
                ->onlyOnForms();
        }

        yield DateField::new('createdAt')->hideOnForm()->hideOnIndex();
        yield DateField::new('updatedAt')->hideOnForm();
        yield AssociationField::new('createdBy', new TranslatableMessage('Created by', [], 'admin'))
            ->setPermission(Role::USER_ADMIN->value)
            ->hideOnForm();
    }

    public function createNewFormBuilder(EntityDto $entityDto, KeyValueStore $formOptions, AdminContext $context): FormBuilderInterface
    {
        $formBuilder = parent::createNewFormBuilder($entityDto, $formOptions, $context);

        return $this->addPasswordEventListener($formBuilder);
    }

    private function addPasswordEventListener(FormBuilderInterface $formBuilder): FormBuilderInterface
    {
        return $formBuilder->addEventListener(FormEvents::POST_SUBMIT, $this->hashPassword());
    }

    private function hashPassword()
    {
        return function ($event) {
            $form = $event->getForm();
            if (!$form->isValid()) {
                return;
            }
            $password = $form->get('password')->getData();
            if (null === $password) {
                return;
            }

            $hash = $this->userPasswordHasher->hashPassword($this->getUser(), $password);
            $form->getData()->setPassword($hash);
        };
    }
}
