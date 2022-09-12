<?php

declare(strict_types=1);

namespace App\OAuth\Security\Token;

use ArrayAccess;

/**
 * @template-extends ArrayAccess<string, TokenInterface>
 */
interface TokenStorageInterface extends ArrayAccess
{
}
