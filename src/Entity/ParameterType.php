<?php

declare(strict_types=1);

namespace App\Entity;

enum ParameterType: string
{
    case Array = 'array';
    case Choice = 'choice';
    case Url = 'url';
    case String = 'string';
    case Boolean = 'boolean';
    case Entity = 'entity';
}
