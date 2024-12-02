<?php

declare(strict_types=1);

namespace Behat\Tests\Config;

use Behat\Config\Config;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Behat\Testwork\ServiceContainer\Exception\ConfigurationLoadingException;
use PHPUnit\Framework\TestCase;

final class ProfileTest extends TestCase
{
    public function testProfileCanBeConvertedIntoAnArray(): void
    {
        $profile = new Profile('default');

        $this->assertIsArray($profile->toArray());
    }

    public function testItReturnsSettings(): void
    {
        $settings = [
            'extensions' => [
                'some_extension' => [],
            ],
        ];

        $profile = new Profile('default', $settings);

        $this->assertEquals($settings, $profile->toArray());
    }

    public function testAddingSuites(): void
    {
        $profile = new Profile('default');
        $profile
            ->withSuite(new Suite('admin_dashboard'))
            ->withSuite(new Suite('managing_administrators'))
        ;

        $this->assertEquals([
            'suites' => [
                'admin_dashboard' => [],
                'managing_administrators' => [],
            ],
        ], $profile->toArray());
    }

    public function testItThrowsAnExceptionWhenAddingExistingSuite(): void
    {
        $profile = new Profile('default');

        $profile->withSuite(new Suite('admin_dashboard'));

        $this->expectException(ConfigurationLoadingException::class);
        $this->expectExceptionMessage('The suite "admin_dashboard" already exists.');

        $profile->withSuite(new Suite('admin_dashboard'));
    }
}