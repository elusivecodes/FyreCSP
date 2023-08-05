<?php
declare(strict_types=1);

namespace Fyre\Security;

use Fyre\Security\Exceptions\CspException;
use Fyre\Server\ClientResponse;

use const JSON_UNESCAPED_SLASHES;

use function array_key_exists;
use function json_encode;

/**
 * CspBuilder
 */
abstract class CspBuilder
{

    public const DEFAULT = 'default';

    public const REPORT = 'report';

    protected const POLICY_HEADERS = [
        'default' => 'Content-Security-Policy',
        'report' => 'Content-Security-Policy-Report-Only'
    ];

    protected static array $policies = [];

    protected static array $reportTo = [];

    /**
     * Add CSP headers to a ClientResponse.
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The new ClientResponse.
     */
    public static function addHeaders(ClientResponse $response): ClientResponse
    {
        foreach (static::$policies AS $key => $policy) {
            if (!array_key_exists($key, static::POLICY_HEADERS)) {
                continue;
            }

            $response = $response->setHeader(static::POLICY_HEADERS[$key], $policy->getHeader());
        }

        if (static::$reportTo !== []) {
            $response = $response->setHeader('Report-To', json_encode(static::$reportTo, JSON_UNESCAPED_SLASHES));
        }

        return $response;
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
     * @throws CspException if the policy is not valid.
     */
    public static function createPolicy(string $key, array $directives = []): Policy
    {
        return static::$policies[$key] = new Policy($directives);
    }

    /**
     * Get a policy.
     * @param string $key The policy key.
     * @return Policy|null The Policy.
     */
    public static function getPolicy(string $key): Policy|null
    {
        return static::$policies[$key] ?? null;
    }

    /**
     * Get the Report-To values.
     * @return array The Report-To values.
     */
    public static function getReportTo(): array
    {
        return static::$reportTo;
    }

    /**
     * Get all policies.
     * @return array The policies.
     */
    public static function getPolicies(): array
    {
        return static::$policies;
    }

    /**
     * Determine if a policy exists.
     * @param string $key The policy key.
     * @return bool TRUE if the policy exists, otherwise FALSE.
     */
    public static function hasPolicy(string $key): bool
    {
        return array_key_exists($key, static::$policies);
    }

    /**
     * Set a policy.
     * @param string $key The policy key.
     * @param Policy $policy The Policy.
     */
    public static function setPolicy(string $key, Policy $policy): void
    {
        static::$policies[$key] = $policy;
    }

    /**
     * Set the Report-To values.
     * @param array $reportTo The Report-To values.
     */
    public static function setReportTo(array $reportTo): void
    {
        static::$reportTo = $reportTo;
    }

}
