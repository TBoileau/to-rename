<?php

declare(strict_types=1);

namespace App\EasyAdmin\Field;

use EasyCorp\Bundle\EasyAdminBundle\Contracts\Field\FieldInterface;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldTrait;

final class StateField implements FieldInterface
{
    use FieldTrait;

    public static function new(string $propertyName, ?string $label = null): StateField
    {
        return (new self())
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setTemplatePath('admin/field/state.html.twig');
    }

    public function setWorkflowName(string $workflowName): self
    {
        $this->setCustomOption('workflow', $workflowName);

        return $this;
    }
}
