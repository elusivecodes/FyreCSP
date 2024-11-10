<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Security\CspBuilder;
use Fyre\Security\Policy;
use PHPUnit\Framework\TestCase;

final class CspBuilderTest extends TestCase
{
    protected CspBuilder $cspBuilder;

    public function testCreatePolicy(): void
    {
        $policy = $this->cspBuilder->createPolicy('default', [
            'default-src' => 'self',
            'child-src' => 'none',
        ]);

        $policy = $this->cspBuilder->getPolicy('default');

        $this->assertInstanceOf(
            Policy::class,
            $policy
        );

        $this->assertSame(
            'default-src \'self\'; child-src \'none\';',
            $policy->getHeader()
        );
    }

    public function testGetInvalid(): void
    {
        $this->assertNull(
            $this->cspBuilder->getPolicy('invalid')
        );
    }

    public function testGetPolicies(): void
    {
        $this->cspBuilder->createPolicy('default', []);
        $this->cspBuilder->createPolicy('report', []);

        $policies = $this->cspBuilder->getPolicies();

        $this->assertInstanceOf(
            Policy::class,
            $policies['default']
        );

        $this->assertInstanceOf(
            Policy::class,
            $policies['report']
        );
    }

    public function testGetPolicy(): void
    {
        $this->cspBuilder->createPolicy('default', []);

        $policy = $this->cspBuilder->getPolicy('default');

        $this->assertInstanceOf(
            Policy::class,
            $policy
        );
    }

    public function testHasPolicy(): void
    {
        $this->cspBuilder->createPolicy('default', []);

        $this->assertTrue(
            $this->cspBuilder->hasPolicy('default')
        );
    }

    public function testHasPolicyInvalid(): void
    {
        $this->assertFalse(
            $this->cspBuilder->hasPolicy('invalid')
        );
    }

    public function testSetPolicy(): void
    {
        $policy = new Policy();

        $this->assertSame(
            $this->cspBuilder,
            $this->cspBuilder->setPolicy('test', $policy)
        );

        $this->assertSame(
            $policy,
            $this->cspBuilder->getPolicy('test')
        );
    }

    public function testSetReportTo(): void
    {
        $this->assertSame(
            $this->cspBuilder,
            $this->cspBuilder->setReportTo([
                'group' => 'csp-endpoint',
                'max_age' => '10886400',
                'endpoints' => [
                    [
                        'url' => 'https://test.com/csp-report',
                    ],
                ],
            ])
        );

        $this->assertSame(
            [
                'group' => 'csp-endpoint',
                'max_age' => '10886400',
                'endpoints' => [
                    [
                        'url' => 'https://test.com/csp-report',
                    ],
                ],
            ],
            $this->cspBuilder->getReportTo()
        );
    }

    protected function setUp(): void
    {
        $this->cspBuilder = new CspBuilder();
    }
}
