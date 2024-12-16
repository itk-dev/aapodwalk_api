<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractType<string>
 */
class LocationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addModelTransformer(new CallbackTransformer(
                $this->implodeCoordinates(...),
                $this->explodeCoordinates(...)
            ))
        ;
    }

    public function implodeCoordinates(?array $coordinates): string
    {
        // Separate non-empty values with comma.
        return implode(', ', array_filter($coordinates ?? []));
    }

    public function explodeCoordinates(string $coordinates): array
    {
        $values = array_map('trim', preg_split('/\s*,\s*/', $coordinates));
        if (2 !== count($values)) {
            throw new TransformationFailedException(invalidMessage: 'The value must contain two numbers separated by comma.', invalidMessageParameters: [/* @todo Make this work! */ 'translation_domain' => 'admin']);
        }

        foreach ($values as $value) {
            if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
                throw new TransformationFailedException(invalidMessage: '{value} is not a valid number', invalidMessageParameters: ['value' => $value, 'translation_domain' => 'admin']);
            }
        }

        return $values;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'help' => new TranslatableMessage('Enter a location on the form <code>lat, lng</code>, e.g. <code>56.153244272970866, 10.213798100000005</code>. <strong>Notice</strong>: the two numbers must always be separated by a comma and use dot as decimal separator.<br>Hint: Open <a href="https://www.google.com/maps" target="_blank">Google Maps</a> and “right” click on the map to get the coordinates.', [], 'admin'),
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
