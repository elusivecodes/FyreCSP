<?php
declare(strict_types=1);

namespace Fyre\Security;

use Fyre\Security\Exceptions\CspException;
use Fyre\Utility\Traits\MacroTrait;

use function array_key_exists;
use function array_map;
use function implode;
use function in_array;
use function is_string;
use function preg_match;

/**
 * Policy
 */
class Policy
{
    use MacroTrait;

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
        'worker-src',
    ];

    protected const VALID_SOURCES = [
        'none',
        'report-sample',
        'self',
        'strict-dynamic',
        'unsafe-eval',
        'unsafe-hashes',
        'unsafe-inline',
    ];

    protected array $directives = [];

    /**
     * New ContentSecurityPolicy constructor.
     *
     * @param array $directives The policy directives.
     */
    public function __construct(array $directives = [])
    {
        foreach ($directives as $directive => $values) {
            static::checkDirective($directive);

            if ($values === false) {
                continue;
            }

            $this->directives[$directive] = [];

            if ($values === true) {
                $values = [];
            } else if (is_string($values)) {
                $values = [$values];
            }

            foreach ($values as $v) {
                $this->directives[$directive][] = $v;
            }
        }
    }

    /**
     * Get the header string.
     *
     * @return string The header string.
     */
    public function __toString(): string
    {
        return $this->getHeader();
    }

    /**
     * Add options to a directive.
     *
     * @param string $directive The directive.
     * @param array|bool|string $value The value.
     * @return Policy A new Policy.
     *
     * @throws CspException if the directive is not valid.
     */
    public function addDirective(string $directive, array|bool|string $value = true): Policy
    {
        if ($value === false) {
            return $this->removeDirective($directive);
        }

        static::checkDirective($directive);

        $temp = clone $this;

        $temp->directives[$directive] ??= [];

        if ($value === true) {
            $value = [];
        } else if (is_string($value)) {
            $value = [$value];
        }

        foreach ($value as $v) {
            if (in_array($v, $temp->directives[$directive])) {
                continue;
            }

            $temp->directives[$directive][] = $v;
        }

        return $temp;
    }

    /**
     * Get the options for a directive.
     *
     * @param string $directive The directive.
     * @return array|null The directive options.
     */
    public function getDirective(string $directive): array|null
    {
        static::checkDirective($directive);

        return $this->directives[$directive] ?? null;
    }

    /**
     * Get the header string.
     *
     * @return string The header string.
     */
    public function getHeader(): string
    {
        $directives = [];

        foreach ($this->directives as $directive => $values) {
            $valueString = $directive;

            if ($values !== []) {
                $valueString .= ' ';
                $valueString .= static::formatSrc($values);
            }

            $directives[] = $valueString.';';
        }

        return implode(' ', $directives);
    }

    /**
     * Determine whether a directive exists.
     *
     * @param string $directive The directive.
     * @return bool TRUE if the directive exists, otherwise FALSE.
     */
    public function hasDirective(string $directive): bool
    {
        static::checkDirective($directive);

        return array_key_exists($directive, $this->directives);
    }

    /**
     * Remove a directive.
     *
     * @param string $directive The directive.
     * @return Policy A New Policy.
     */
    public function removeDirective(string $directive): static
    {
        static::checkDirective($directive);

        $temp = clone $this;

        unset($temp->directives[$directive]);

        return $temp;
    }

    /**
     * Determine whether a directive is valid.
     *
     * @param string $directive The directive.
     *
     * @throws CspException if the directive is not valid.
     */
    protected static function checkDirective(string $directive): void
    {
        if (!in_array($directive, static::VALID_DIRECTIVES)) {
            throw CspException::forInvalidDirective($directive);
        }
    }

    /**
     * Format source values from an array.
     *
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
            $sources
        );

        return implode(' ', $sources);
    }
}
