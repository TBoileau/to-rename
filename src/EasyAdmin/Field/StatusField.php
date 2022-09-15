<?php

declare(strict_types=1);

namespace App\EasyAdmin\Field;

use App\Entity\Status;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class StatusField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): StatusField
    {
        return (new self())
            ->setFormType(EnumType::class)
            ->setFormTypeOption('class', Status::class)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/status.html.twig');
    }
}
