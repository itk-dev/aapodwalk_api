<?php

namespace App\Twig\Runtime;

use App\Admin\Field\ValueWithUnitField;
use App\Entity\PointOfInterest;
use App\Form\ValueWithUnitType;
use App\Service\MediaProcessorInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use Twig\Extension\RuntimeExtensionInterface;

class AppExtensionRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly MediaProcessorInterface $mediaProcessor,
        private readonly ValueWithUnitType $valueWithUnitType,
    ) {
    }

    public function getMediaEmbedCode(mixed $value): ?string
    {
        try {
            if ($value instanceof PointOfInterest) {
                return $this->mediaProcessor->getEmbedCode($value);
            }
        } catch (\Throwable $exception) {
            // Ignore all errors.
        }

        return null;
    }

    public function getMediaTemplates(): array
    {
        return $this->mediaProcessor->getTemplates();
    }

    public function formatValueWithUnit(FieldDto $field): string
    {
        if (ValueWithUnitField::class !== $field->getFieldFqcn()) {
            throw new \InvalidArgumentException(sprintf("Field's FQCN must be %s (or a subclass). Found %s.", ValueWithUnitField::class, $field->getFieldFqcn()));
        }
        if (ValueWithUnitType::class !== $field->getFormType()) {
            throw new \InvalidArgumentException(sprintf("Field's form type must be %s. Found %s.", ValueWithUnitType::class, $field->getFormType()));
        }

        return $this->valueWithUnitType->getFormattedValue($field->getValue(), $field->getFormTypeOptions());
    }
}
