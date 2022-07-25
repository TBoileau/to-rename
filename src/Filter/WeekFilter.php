<?php

declare(strict_types=1);

namespace App\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\AbstractContextAwareFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Live;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\PropertyInfo\Type;

final class WeekFilter extends AbstractContextAwareFilter
{
    protected function filterProperty(
        string $property,
        $value,
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        string $operationName = null
    ): void {
        if (Live::class !== $resourceClass) {
            return; // @codeCoverageIgnore
        }
        $parameterName = $queryNameGenerator->generateParameterName($property);

        $predicate = match ($property) {
            'week' => 'WEEK(o.startedAt)',
            'year' => 'YEAR(o.startedAt)',
            default => '' // @codeCoverageIgnore
        };

        $queryBuilder
            ->andWhere(sprintf('%s = :%s', $predicate, $parameterName))
            ->setParameter($parameterName, $value);
    }

    /**
     * @return array<string, mixed>
     */
    public function getDescription(string $resourceClass): array
    {
        return [
            'week' => [
                'property' => 'week',
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => true,
                'swagger' => [
                    'name' => 'Week of the year',
                    'type' => 'Integer',
                ],
            ],
            'year' => [
                'property' => 'year',
                'type' => Type::BUILTIN_TYPE_INT,
                'required' => true,
                'swagger' => [
                    'name' => 'Year',
                    'type' => 'Integer',
                ],
            ],
        ];
    }
}
