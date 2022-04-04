<?php
declare(strict_types=1);

namespace Fyre\CSP\Exceptions;

use
    RuntimeException;

/**
 * CspException
 */
class CspException extends RuntimeException
{

    public static function forInvalidDirective(string $directive)
    {
        return new static('CSP invalid directive: '.$directive);
    }

}
