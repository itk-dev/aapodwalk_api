<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\LocaleSwitcher;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @extends AbstractType<int>
 */
final class ValueWithUnitType extends AbstractType
{
    public const string FIELD_VALUE = 'value';
    public const string FIELD_UNIT = 'unit';

    public const string OPTION_LABEL = 'label';
    public const string OPTION_SCALE = 'scale';
    public const string OPTION_LOCALIZED_UNIT = 'localized_unit';

    private const int SCALE = 1;

    private \NumberFormatter $numberFormatter;

    public function __construct(
        private readonly TranslatorInterface $translator,
        LocaleSwitcher $localeSwitcher,
    ) {
        $this->numberFormatter = new \NumberFormatter($localeSwitcher->getLocale(), \NumberFormatter::DECIMAL);
        $this->numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, self::SCALE);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $units = array_keys($options['units']);
        $unitChoices = array_combine($units, $units);
        $builder
            ->add(self::FIELD_VALUE, NumberType::class, [
                'scale' => self::SCALE,
            ])
            ->add(self::FIELD_UNIT, ChoiceType::class, [
                'choices' => $unitChoices,
                'choice_label' => function ($choice, string $key, mixed $value) use ($options): TranslatableMessage|string {
                    return $options['units'][$key]['label'] ?? $key;
                },
            ]);

        $builder
            ->addModelTransformer(new CallbackTransformer(
                fn ($value) => $this->transform($value, $options),
                fn ($value) => $this->reverseTransform($value, $options),
            ))
        ;
    }

    public function transform(?int $value, array $options): array
    {
        try {
            return $this->getMatchingUnit($value, $options);
        } catch (\Exception $exception) {
            throw new TransformationFailedException(invalidMessage: 'Error transforming value: {value}.', invalidMessageParameters: ['value' => $value, /* @todo Make this work! */ 'translation_domain' => 'admin']);
        }
    }

    public function reverseTransform(array $values, array $options): int
    {
        [self::FIELD_VALUE => $value, self::FIELD_UNIT => $unit] = $values;
        $units = $this->getUnits($options);
        $info = $units[$unit] ?? null;

        if (null === $info) {
            throw new TransformationFailedException(invalidMessage: 'Invalid unit: {unit}.', invalidMessageParameters: ['unit' => $unit, /* @todo Make this work! */ 'translation_domain' => 'admin']);
        }

        return $value * $info[self::OPTION_SCALE];
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setRequired('units')
            ->setAllowedTypes('units', 'array')
            ->setDefault('units', function (OptionsResolver $resolver): void {
                $resolver
                    ->setPrototype(true)
                    ->setDefault(self::OPTION_SCALE, 1)
                    ->setAllowedTypes(self::OPTION_SCALE, 'int')
                    ->setAllowedValues(self::OPTION_SCALE, Validation::createIsValidCallable(
                        new Positive(),
                    ))
                    ->setRequired(self::OPTION_LABEL)
                    ->setAllowedTypes(self::OPTION_LABEL, ['string', TranslatableMessage::class])
                    ->setRequired(self::OPTION_LOCALIZED_UNIT);
            })
            // Make units required.
            ->setAllowedValues('units', static fn (array $value) => !empty($value))
        ;
    }

    /**
     * Get units sorted descending by scale.
     */
    private function getUnits(array $options): array
    {
        $units = $options['units'];

        uasort($units, static fn ($a, $b) => -($a[self::OPTION_SCALE] <=> $b[self::OPTION_SCALE]));

        return $units;
    }

    public function getMatchingUnit(?int $value, array $options): array
    {
        $units = $this->getUnits($options);
        foreach ($units as $unit => $info) {
            $scale = $info[self::OPTION_SCALE];
            if ($value >= $scale || array_key_last($units) === $unit) {
                return [
                    self::FIELD_VALUE => null === $value ? null : ($scale > 1 ? $value / $scale : $value),
                    self::FIELD_UNIT => $unit,
                    self::OPTION_LOCALIZED_UNIT => $info[self::OPTION_LOCALIZED_UNIT],
                ];
            }
        }

        throw new \RuntimeException('This should never be called.');
    }

    public function getFormattedValue(int $value, array $options): string
    {
        $unit = $this->getMatchingUnit($value, $options);

        // @todo There must be a better way to do this!
        return sprintf('%s %s', $this->numberFormatter->format($unit['value']),
            $unit[self::OPTION_LOCALIZED_UNIT]->trans($this->translator));
    }
}
