<?php
declare(strict_types=1);

namespace Fyre\CSP\Exceptions;

use
    Fyre\Error\Exceptions\Exception;

/**
 * CspException
 */
class CspException extends Exception
{

    public static function forInvalidDirective(string $directive)
    {
        return new static('CSP invalid directive: '.$directive);
    }

}
