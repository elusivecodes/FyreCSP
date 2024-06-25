<?php
declare(strict_types=1);

namespace Fyre\Security\Exceptions;

use RuntimeException;

/**
 * CspException
 */
class CspException extends RuntimeException
{
    public static function forInvalidDirective(string $directive): static
    {
        return new static('CSP invalid directive: '.$directive);
    }
}
