<?php
declare(strict_types=1);

namespace Fyre\CSP;

use
    Fyre\Server\ClientResponse;

use function
    array_key_exists;

/**
 * CspBuilder
 */
abstract class CspBuilder
{

    protected const POLICY_HEADERS = [
        'policy' => 'Content-Security-Policy',
        'report' => 'Content-Security-Policy-Report-Only'
    ];

    protected static array $policies = [];

    /**
     * Add CSP headers to a ClientResponse.
     * @param ClientResponse $response The ClientResponse.
     */
    public static function addHeaders(ClientResponse $response): void
    {
        foreach (static::$policies AS $key => $policy) {
            if (!array_key_exists($key, static::POLICY_HEADERS)) {
                continue;
            }

            $response->setHeader(static::POLICY_HEADERS[$key], $policy->getHeader());
        }
    }

    /**
     * Clear all policies.
     */
    public static function clear(): void
    {
        static::$policies = [];
    }

    /**
     * Create a policy.
     * @param string $key The policy key.
     * @param array $directives The policy directives.
     * @return Policy The Policy.
     */
    public static function create(string $key, array $directives = []): Policy
    {
        return static::$policies[$key] = new Policy($directives);
    }

    /**
     * Get a policy.
     * @param string $key The policy key.
     * @return Policy|null The Policy.
     */
    public static function get(string $key): Policy|null
    {
        return static::$policies[$key] ?? null;
    }

    /**
     * Get all policies.
     * @return array The policies.
     */
    public static function getPolicies(): array
    {
        return static::$policies;
    }

}
