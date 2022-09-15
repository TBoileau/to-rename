<?php

declare(strict_types=1);

namespace App\Entity;

enum Status: string
{
    case Private = 'private';

    case Public = 'public';

    case Unlisted = 'unlisted';
}
