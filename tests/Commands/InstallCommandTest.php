<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class InstallCommandTest extends TestCase
{
    public function test_install_command_will_create_a_fallback_locale()
    {
        $this->fallback_locale->delete();

        $this->assertTrue(Locale::all()->isEmpty());

        $this->artisan('make:i18n')->run();

        $this->assertNotNull(Locale::getFallbackLocale());
    }
}