<?php

declare(strict_types=1);

namespace App\EasyAdmin\Filter;

use App\Entity\Status;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Contracts\Filter\FilterInterface;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FilterDataDto;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterTrait;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

final class StatusFilter implements FilterInterface
{
    use FilterTrait;

    public static function new(string $propertyName, string $label = null): self
    {
        return (new self())
            ->setFilterFqcn(__CLASS__)
            ->setProperty($propertyName)
            ->setLabel($label)
            ->setFormTypeOption('class', Status::class)
            ->setFormType(EnumType::class);
    }

    public function apply(
        QueryBuilder $queryBuilder,
        FilterDataDto $filterDataDto,
        FieldDto|null $fieldDto,
        EntityDto $entityDto
    ): void {
        $queryBuilder
            ->andWhere(sprintf('%s.%s = :status', $filterDataDto->getEntityAlias(), $filterDataDto->getProperty()))
            ->setParameter('status', $filterDataDto->getValue());
    }
}
