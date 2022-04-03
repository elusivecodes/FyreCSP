<?php
declare(strict_types=1);

namespace Fyre\CSP;

use
    Fyre\CSP\Exceptions\CspException;

use function
    array_keys,
    array_map,
    implode,
    in_array,
    is_strring,
    preg_match;

/**
 * Policy
 */
class Policy
{

    protected const VALID_DIRECTIVES = [
        'base-uri',
        'block-all-mixed-content',
        'child-src',
        'connect-src',
        'default-src',
        'font-src',
        'form-action',
        'frame-src',
        'frame-ancestors',
        'img-src',
        'manifest-src',
        'media-src',
        'object-src',
        'plugin-types',
        'prefetch-src',
        'report-uri',
        'report-to',
        'sandbox',
        'script-src',
        'script-src-attr',
        'script-src-elem',
        'style-src',
        'style-src-attr',
        'style-src-elem',
        'upgrade-insecure-requests',
        'webrtc-src',
        'worker-src'
    ];

    protected const VALID_SOURCES = [
        'none',
        'report-sample',
        'self',
        'strict-dynamic',
        'unsafe-eval',
        'unsafe-hashes',
        'unsafe-inline'
    ];

    protected array $directives = [];

    /**
     * New ContentSecurityPolicy constructor.
     * @param array $directives The policy directives.
     */
    public function __construct(array $directives = [])
    {
        foreach ($directives AS $directive => $values) {
            $this->addDirective($directive, $values);
        }
    }

    /**
     * Get the header string.
     * @return string The header string.
     */
    public function __toString(): string
    {
        return $this->getHeader();
    }

    /**
     * Add options to a directive.
     * @param string $directive The directive.
     * @param string|array|bool $value The value.
     * @return Policy The Policy.
     * @throws CspException if the directive is invalid.
     */
    public function addDirective(string $directive, string|array|bool $value): Policy
    {
        if (!in_array($directive, static::VALID_DIRECTIVES)) {
            throw CspException::forInvalidDirective($directive);
        }

        if ($value === false) {
            unset($this->directives[$directive]);
            return $this;
        }

        if ($value === true) {
            $value = [];
        } else if (is_string($value)) {
            $value = [$value];
        }

        $this->directives[$directive] ??= [];

        foreach ($value AS $val) {
            $this->directives[$directive][$val] = true;
        }

        return $this;
    }

    /**
     * Get the header string.
     * @return string The header string.
     */
    public function getHeader(): string
    {
        $directives = [];

        foreach ($this->directives AS $directive => $values) {
            $valueString = $directive.' '. static::formatSrc($values);
            $directives[] = trim($valueString).';';
        }

        return implode(' ', $directives);
    }

    /**
     * Format source values from an array.
     * @param array $sources The sources.
     * @return string The formatted string.
     */
    protected static function formatSrc(array $sources): string
    {
        $sources = array_map(
            function(string $source): string {
                if (in_array($source, static::VALID_SOURCES) || preg_match('/^(nonce|sha(256|384|512)\-).+$/', $source)) {
                    return '\''.$source.'\'';
                }

                return $source;
            },
            array_keys($sources)
        );

        return implode(' ', $sources);
    }

}
