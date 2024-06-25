<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Security\CspBuilder;
use Fyre\Security\Policy;
use PHPUnit\Framework\TestCase;

final class CspBuilderTest extends TestCase
{
    public function testCreatePolicy(): void
    {
        $policy = CspBuilder::createPolicy('default', [
            'default-src' => 'self',
            'child-src' => 'none',
        ]);

        $policy = CspBuilder::getPolicy('default');

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
            CspBuilder::getPolicy('invalid')
        );
    }

    public function testGetPolicies(): void
    {
        CspBuilder::createPolicy('default', []);
        CspBuilder::createPolicy('report', []);

        $policies = CspBuilder::getPolicies();

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
        CspBuilder::createPolicy('default', []);

        $policy = CspBuilder::getPolicy('default');

        $this->assertInstanceOf(
            Policy::class,
            $policy
        );
    }

    public function testHasPolicy(): void
    {
        CspBuilder::createPolicy('default', []);

        $this->assertTrue(
            CspBuilder::hasPolicy('default')
        );
    }

    public function testHasPolicyInvalid(): void
    {
        $this->assertFalse(
            CspBuilder::hasPolicy('invalid')
        );
    }

    public function testSetPolicy(): void
    {
        $policy = new Policy();

        CspBuilder::setPolicy('test', $policy);

        $this->assertSame(
            $policy,
            CspBuilder::getPolicy('test')
        );
    }

    public function testSetReportTo(): void
    {
        CspBuilder::setReportTo([
            'group' => 'csp-endpoint',
            'max_age' => '10886400',
            'endpoints' => [
                [
                    'url' => 'https://test.com/csp-report',
                ],
            ],
        ]);

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
            CspBuilder::getReportTo()
        );
    }

    protected function setUp(): void
    {
        CspBuilder::clear();
    }
}
