<?php
declare(strict_types=1);

namespace Tests;

use Fyre\Security\Exceptions\CSPException;
use Fyre\Security\Policy;
use Fyre\Utility\Traits\MacroTrait;
use PHPUnit\Framework\TestCase;

use function class_uses;

final class PolicyTest extends TestCase
{
    public function testAddDirective(): void
    {
        $policy1 = new Policy();
        $policy2 = $policy1->addDirective('default-src', 'self');

        $this->assertSame(
            '',
            $policy1->getHeader()
        );

        $this->assertSame(
            'default-src \'self\';',
            $policy2->getHeader()
        );
    }

    public function testAddDirectiveArray(): void
    {
        $policy1 = new Policy();
        $policy2 = $policy1->addDirective('default-src', [
            'self',
            'https://test.com/',
        ]);

        $this->assertSame(
            '',
            $policy1->getHeader()
        );

        $this->assertSame(
            'default-src \'self\' https://test.com/;',
            $policy2->getHeader()
        );
    }

    public function testAddDirectiveFalse(): void
    {
        $policy1 = new Policy([
            'upgrade-insecure-requests' => true,
        ]);
        $policy2 = $policy1->addDirective('upgrade-insecure-requests', false);

        $this->assertSame(
            'upgrade-insecure-requests;',
            $policy1->getHeader()
        );

        $this->assertSame(
            '',
            $policy2->getHeader()
        );
    }

    public function testAddDirectiveInvalid(): void
    {
        $this->expectException(CSPException::class);

        $policy = new Policy();

        $policy->addDirective('invalid', true);
    }

    public function testAddDirectiveMerge(): void
    {
        $policy1 = new Policy([
            'default-src' => 'self',
        ]);
        $policy2 = $policy1->addDirective('default-src', 'https://test.com/');

        $this->assertSame(
            'default-src \'self\';',
            $policy1->getHeader()
        );

        $this->assertSame(
            'default-src \'self\' https://test.com/;',
            $policy2->getHeader()
        );
    }

    public function testAddDirectiveTrue(): void
    {
        $policy1 = new Policy();
        $policy2 = $policy1->addDirective('upgrade-insecure-requests', true);

        $this->assertSame(
            '',
            $policy1->getHeader()
        );

        $this->assertSame(
            'upgrade-insecure-requests;',
            $policy2->getHeader()
        );
    }

    public function testAddDirectiveUnique(): void
    {
        $policy1 = new Policy([
            'default-src' => 'self',
        ]);
        $policy2 = $policy1->addDirective('default-src', 'self');

        $this->assertSame(
            'default-src \'self\';',
            $policy1->getHeader()
        );

        $this->assertSame(
            'default-src \'self\';',
            $policy2->getHeader()
        );
    }

    public function testGetDirective(): void
    {
        $policy = new Policy([
            'default-src' => [
                'self',
                'https://test.com/',
            ],
        ]);

        $this->assertSame(
            [
                'self',
                'https://test.com/',
            ],
            $policy->getDirective('default-src')
        );
    }

    public function testGetDirectiveInvalid(): void
    {
        $this->expectException(CSPException::class);

        $policy = new Policy();

        $policy->getDirective('invalid');
    }

    public function testHasDirective(): void
    {
        $policy = new Policy([
            'default-src' => 'self',
        ]);

        $this->assertTrue(
            $policy->hasDirective('default-src')
        );
    }

    public function testHasDirectiveFalse(): void
    {
        $policy = new Policy();

        $this->assertFalse(
            $policy->hasDirective('default-src')
        );
    }

    public function testHasDirectiveInvalid(): void
    {
        $this->expectException(CSPException::class);

        $policy = new Policy();

        $policy->hasDirective('invalid');
    }

    public function testMacroable(): void
    {
        $this->assertContains(
            MacroTrait::class,
            class_uses(Policy::class)
        );
    }

    public function testRemoveDirective(): void
    {
        $policy1 = new Policy([
            'upgrade-insecure-requests' => true,
        ]);
        $policy2 = $policy1->removeDirective('upgrade-insecure-requests');

        $this->assertSame(
            'upgrade-insecure-requests;',
            $policy1->getHeader()
        );

        $this->assertSame(
            '',
            $policy2->getHeader()
        );
    }

    public function testRemoveDirectiveInvalid(): void
    {
        $this->expectException(CSPException::class);

        $policy = new Policy();

        $policy->removeDirective('invalid');
    }
}
