<?php

declare(strict_types=1);

namespace App\EasyAdmin\Field;

use App\Form\DurationType;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class DurationField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): DurationField
    {
        return (new self())
            ->setFormType(DurationType::class)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/duration.html.twig');
    }
}
