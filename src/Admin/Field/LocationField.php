<?php

namespace App\Admin\Field;

use App\Form\LocationType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Contracts\Translation\TranslatableInterface;

final class LocationField implements FieldInterface
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

            // this template is used in 'index' and 'detail' pages
            ->setTemplatePath('admin/field/location.html.twig')

            // this is used in 'edit' and 'new' pages to edit the field contents
            // you can use your own form types too
            ->setFormType(LocationType::class)
            ->addCssClass('field-location');
    }
}
