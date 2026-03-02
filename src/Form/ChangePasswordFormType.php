<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;
use Symfony\Component\Validator\Constraints\PasswordStrength;

use function Symfony\Component\Translation\t;

/**
 * @extends AbstractType<mixed>
 */
class ChangePasswordFormType extends AbstractType
{
    #[\Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'options' => [
                    'attr' => [
                        'autocomplete' => 'new-password',
                    ],
                ],
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => t('Please enter a password', [], 'admin'),
                        ]),
                        new Length(
                            min: 12,
                            max: 4096,
                            minMessage: (string) t('Your password should be at least {{ limit }} characters', [], 'admin'),
                        ),
                        new PasswordStrength(),
                        new NotCompromisedPassword(),
                    ],
                    'label' => t('New password', [], 'admin'),
                ],
                'second_options' => [
                    'label' => t('Repeat password', [], 'admin'),
                ],
                'invalid_message' => t('The password fields must match.', [], 'admin'),
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
        ;
    }

    #[\Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
