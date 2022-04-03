<?php
declare(strict_types=1);

namespace Fyre\CSP\Exceptions;

use
    RunTimeException;

/**
 * CspException
 */
class CspException extends RunTimeException
{

    public static function forInvalidDirective(string $directive)
    {
        return new static('CSP invalid directive: '.$directive);
    }

}
