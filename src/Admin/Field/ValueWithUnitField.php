<?php

namespace App\Admin\Field;

use App\Form\ValueWithUnitType;
use App\Service\ValueWithUnitHelper;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class ValueWithUnitField implements FieldInterface
{
    use FieldTrait;

    /**
     * @param TranslatableInterface|string|false|null $label
     */
    public static function new(string $propertyName, $label = null): self
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setScale(0)

            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('admin/field/value_with_unit.html.twig')

            // this is used in 'edit' and 'new' pages to edit the field contents
            // you can use your own form types too
            ->setFormType(ValueWithUnitType::class)
            ->addCssClass('field-value-with-unit');
    }

    public function setUnits(array $units): self
    {
        return $this->setFormTypeOption(ValueWithUnitHelper::OPTION_UNITS, $units);
    }

    public function setScale(int $scale): self
    {
        return $this->setFormTypeOption(ValueWithUnitHelper::OPTION_SCALE, $scale);
    }
}
