<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Doctrine\Entity\Status;
use Doctrine\DBAL\Platforms\AbstractPlatform;

final class StatusType extends AbstractEnumType
{
    public const NAME = 'status';

    public function getName(): string
    {
        return self::NAME;
    }

    public static function getEnumsClass(): string
    {
        return Status::class;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'varchar(8)';
    }
}
