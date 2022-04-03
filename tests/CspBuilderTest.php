<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\CSP\CspBuilder,
    Fyre\CSP\Policy,
    PHPUnit\Framework\TestCase;

final class CspBuilderTest extends TestCase
{

    public function testCreate(): void
    {
        $policy = CspBuilder::create('default', [
            'default-src' => 'self',
            'child-src' => 'none'
        ]);

        $policy = CspBuilder::get('default');

        $this->assertInstanceOf(
            Policy::class,
            $policy
        );

        $this->assertSame(
            'default-src \'self\'; child-src \'none\';',
            $policy->getHeader()
        );
    }

    public function testGet(): void
    {
        CspBuilder::create('default', []);

        $policy = CspBuilder::get('default');

        $this->assertInstanceOf(
            Policy::class,
            $policy
        );
    }

    public function testGetInvalid(): void
    {
        $this->assertNull(
            CspBuilder::get('invalid')
        );
    }

    public function testGetPolicies(): void
    {
        CspBuilder::create('default', []);
        CspBuilder::create('report', []);

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

    protected function setUp(): void
    {
        CspBuilder::clear();
    }
}
