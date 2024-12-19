<?php

namespace App\Service;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Validation;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ValueWithUnitHelper
{
    public const string OPTION_UNITS = 'units';
    public const string OPTION_SCALE = 'scale';
    public const string OPTION_UNIT_LABEL = 'label';
    public const string OPTION_UNIT_FACTOR = 'factor';
    public const string OPTION_UNIT_LOCALIZED_UNIT = 'localized_unit';

    public const string FIELD_VALUE = 'value';
    public const string FIELD_UNIT = 'unit';

    private array $options;
    private \NumberFormatter $numberFormatter;

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    private static array $clones = [];

    public function withOptions(
        array $options,
    ): static {
        $options = array_filter($options, static fn ($key) => in_array($key, [ValueWithUnitHelper::OPTION_UNITS, ValueWithUnitHelper::OPTION_SCALE]), ARRAY_FILTER_USE_KEY);
        $key = sha1(json_encode($options, JSON_THROW_ON_ERROR));
        if (!isset(self::$clones[$key])) {
            $clone = clone $this;
            $clone->options = $this->resolveOptions($options);
            $clone->numberFormatter = new \NumberFormatter($this->translator->getLocale(), \NumberFormatter::DECIMAL);
            $clone->numberFormatter->setAttribute(\NumberFormatter::FRACTION_DIGITS, $clone->options[self::OPTION_SCALE]);
            $clone->numberFormatter->setSymbol(\NumberFormatter::GROUPING_SEPARATOR_SYMBOL, '');
            self::$clones[$key] = $clone;
        }

        return self::$clones[$key];
    }

    public function getScale(): int
    {
        return $this->options[self::OPTION_SCALE] ?? 1;
    }

    /**
     * Get units sorted descending by factor.
     */
    public function getUnits(): array
    {
        $units = $this->options['units'];

        uasort($units, static fn ($a, $b) => -($a[self::OPTION_UNIT_FACTOR] <=> $b[self::OPTION_UNIT_FACTOR]));

        return $units;
    }

    public function getMatchingUnit(?int $value): array
    {
        $units = $this->getUnits();
        foreach ($units as $unit => $info) {
            // Make sure that factor is positive.
            $factor = max(1, $info[self::OPTION_UNIT_FACTOR]);

            //            $isMatch = 0 === $value % $factor;
            $truncatedValue = (float) number_format($value / $factor, $this->getScale(), '.', '');
            $isMatch = (float) ($value / $factor) === $truncatedValue;

            if (($value >= $factor && $isMatch) || array_key_last($units) === $unit) {
                return $info + [
                    self::FIELD_VALUE => null === $value ? null : $truncatedValue,
                    self::FIELD_UNIT => $unit,
                ];
            }
        }

        throw new \RuntimeException('This should never be called.');
    }

    public function getFormattedValue(int $value): string
    {
        $unit = $this->getMatchingUnit($value);

        $localizedUnit = $unit[self::OPTION_UNIT_LOCALIZED_UNIT] ?? $unit[self::FIELD_UNIT];
        if ($localizedUnit instanceof TranslatableMessage) {
            $localizedUnit = $localizedUnit->trans($this->translator);
        }

        // @todo There must be a better way to do this!
        return sprintf('%s %s', $this->numberFormatter->format($unit['value']), $localizedUnit);
    }

    private function resolveOptions(array $options): array
    {
        return (new OptionsResolver())
            ->setRequired(self::OPTION_UNITS)
            ->setAllowedTypes(self::OPTION_UNITS, 'array')
            ->setDefault(self::OPTION_UNITS, function (OptionsResolver $resolver): void {
                $resolver
                    ->setPrototype(true)
                    ->setDefault(self::OPTION_UNIT_FACTOR, 1)
                    ->setAllowedTypes(self::OPTION_UNIT_FACTOR, 'int')
                    ->setAllowedValues(self::OPTION_UNIT_FACTOR, Validation::createIsValidCallable(
                        new Positive(),
                    ))
                    ->setRequired(self::OPTION_UNIT_LABEL)
                    ->setAllowedTypes(self::OPTION_UNIT_LABEL, ['string', TranslatableMessage::class])
                    ->setDefault(self::OPTION_UNIT_LOCALIZED_UNIT, null)
                    ->setAllowedTypes(self::OPTION_UNIT_LOCALIZED_UNIT, ['null', 'string', TranslatableMessage::class]);
            })
            // Make units required.
            ->setAllowedValues(self::OPTION_UNITS, static fn (array $value) => !empty($value))
            ->setRequired(self::OPTION_SCALE)
            ->setAllowedTypes(self::OPTION_SCALE, 'int')
            ->setDefault(self::OPTION_SCALE, 0)
            ->resolve($options);
    }
}
