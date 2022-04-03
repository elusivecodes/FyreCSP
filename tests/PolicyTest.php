<?php
declare(strict_types=1);

namespace Tests;

use
    Fyre\CSP\Exceptions\CSPException,
    Fyre\CSP\Policy,
    PHPUnit\Framework\TestCase;

final class PolicyTest extends TestCase
{

    public function testAddDirective(): void
    {
        $policy = new Policy();

        $this->assertSame(
            $policy,
            $policy->addDirective('default-src', 'self')
        );

        $this->assertSame(
            'default-src \'self\';',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveArray(): void
    {
        $policy = new Policy();

        $policy->addDirective('default-src', [
            'self',
            'https://test.com/'
        ]);

        $this->assertSame(
            'default-src \'self\' https://test.com/;',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveMerge(): void
    {
        $policy = new Policy();

        $policy->addDirective('default-src', 'self');
        $policy->addDirective('default-src', 'https://test.com/');

        $this->assertSame(
            'default-src \'self\' https://test.com/;',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveTrue(): void
    {
        $policy = new Policy();

        $policy->addDirective('upgrade-insecure-requests', true);

        $this->assertSame(
            'upgrade-insecure-requests;',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveFalse(): void
    {
        $policy = new Policy();

        $policy->addDirective('upgrade-insecure-requests', true);
        $policy->addDirective('upgrade-insecure-requests', false);

        $this->assertSame(
            '',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveUnique(): void
    {
        $policy = new Policy();

        $policy->addDirective('default-src', 'self');
        $policy->addDirective('default-src', 'self');

        $this->assertSame(
            'default-src \'self\';',
            $policy->getHeader()
        );
    }

    public function testAddDirectiveInvalid(): void
    {
        $this->expectException(CSPException::class);

        $policy = new Policy();

        $policy->addDirective('invalid', true);
    }

}
