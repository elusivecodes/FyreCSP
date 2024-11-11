<?php
declare(strict_types=1);

namespace Fyre\Security;

use Fyre\Security\Exceptions\CspException;
use Fyre\Server\ClientResponse;

use function array_key_exists;
use function json_encode;

use const JSON_UNESCAPED_SLASHES;

/**
 * ContentSecurityPolicy
 */
class ContentSecurityPolicy
{
    public const DEFAULT = 'default';

    public const REPORT = 'report';

    protected const POLICY_HEADERS = [
        'default' => 'Content-Security-Policy',
        'report' => 'Content-Security-Policy-Report-Only',
    ];

    protected array $policies = [];

    protected array $reportTo = [];

    /**
     * Add ContentSecurityPolicy headers to a ClientResponse.
     *
     * @param ClientResponse $response The ClientResponse.
     * @return ClientResponse The new ClientResponse.
     */
    public function addHeaders(ClientResponse $response): ClientResponse
    {
        foreach (static::POLICY_HEADERS as $key => $header) {
            if (!array_key_exists($key, $this->policies)) {
                continue;
            }

            $value = $this->policies[$key]->getHeader();

            if (!$value) {
                continue;
            }

            $response = $response->setHeader($header, $value);
        }

        if ($this->reportTo !== []) {
            $response = $response->setHeader('Report-To', json_encode($this->reportTo, JSON_UNESCAPED_SLASHES));
        }

        return $response;
    }

    /**
     * Clear all policies.
     */
    public function clear(): void
    {
        $this->policies = [];
    }

    /**
     * Create a policy.
     *
     * @param string $key The policy key.
     * @param array $directives The policy directives.
     * @return Policy The Policy.
     *
     * @throws CspException if the policy is not valid.
     */
    public function createPolicy(string $key, array $directives = []): Policy
    {
        return $this->policies[$key] = new Policy($directives);
    }

    /**
     * Get all policies.
     *
     * @return array The policies.
     */
    public function getPolicies(): array
    {
        return $this->policies;
    }

    /**
     * Get a policy.
     *
     * @param string $key The policy key.
     * @return Policy|null The Policy.
     */
    public function getPolicy(string $key): Policy|null
    {
        return $this->policies[$key] ?? null;
    }

    /**
     * Get the Report-To values.
     *
     * @return array The Report-To values.
     */
    public function getReportTo(): array
    {
        return $this->reportTo;
    }

    /**
     * Determine whether a policy exists.
     *
     * @param string $key The policy key.
     * @return bool TRUE if the policy exists, otherwise FALSE.
     */
    public function hasPolicy(string $key): bool
    {
        return array_key_exists($key, $this->policies);
    }

    /**
     * Set a policy.
     *
     * @param string $key The policy key.
     * @param Policy $policy The Policy.
     * @return ContentSecurityPolicy The ContentSecurityPolicy.
     */
    public function setPolicy(string $key, Policy $policy): static
    {
        $this->policies[$key] = $policy;

        return $this;
    }

    /**
     * Set the Report-To values.
     *
     * @param array $reportTo The Report-To values.
     * @return ContentSecurityPolicy The ContentSecurityPolicy.
     */
    public function setReportTo(array $reportTo): static
    {
        $this->reportTo = $reportTo;

        return $this;
    }
}
