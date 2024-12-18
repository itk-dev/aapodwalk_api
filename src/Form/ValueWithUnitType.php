<?php

namespace App\Form;

use App\Service\ValueWithUnitHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

/**
 * @extends AbstractType<int>
 */
final class ValueWithUnitType extends AbstractType
{
    public const string FIELD_VALUE = 'value';
    public const string FIELD_UNIT = 'unit';

    public function __construct(
        private ValueWithUnitHelper $helper,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->helper = $this->helper->withOptions($options);

        $units = array_keys($options['units']);
        $unitChoices = array_combine($units, $units);
        $builder
            ->add(self::FIELD_VALUE, NumberType::class, [
                'scale' => $this->helper->getScale(),
            ])
            ->add(self::FIELD_UNIT, ChoiceType::class, [
                'choices' => $unitChoices,
                'choice_label' => function ($choice, string $key, mixed $value) use ($options
                ): TranslatableMessage|string {
                    return $options['units'][$key]['label'] ?? $key;
                },
            ]);

        $builder
            ->addModelTransformer(new CallbackTransformer(
                $this->transform(...),
                $this->reverseTransform(...),
            ));
    }

    public function transform(?int $value): array
    {
        try {
            return $this->helper->getMatchingUnit($value);
        } catch (\Exception) {
            throw new TransformationFailedException(invalidMessage: 'Error transforming value: {value}.', invalidMessageParameters: ['value' => $value, /* @todo Make this work! */ 'translation_domain' => 'admin']);
        }
    }

    public function reverseTransform(array $values): int
    {
        [self::FIELD_VALUE => $value, self::FIELD_UNIT => $unit] = $values;
        $units = $this->helper->getUnits();
        $info = $units[$unit] ?? null;

        if (null === $info) {
            throw new TransformationFailedException(invalidMessage: 'Invalid unit: {unit}.', invalidMessageParameters: ['unit' => $unit, /* @todo Make this work! */ 'translation_domain' => 'admin']);
        }

        return $value * $this->helper->getScale();
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault(ValueWithUnitHelper::OPTION_UNITS, [])
            ->setDefault(ValueWithUnitHelper::OPTION_SCALE, 0);
    }
}
