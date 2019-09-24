<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class InstallTest extends TestCase
{
    public function test_install_command_will_create_a_fallback_locale()
    {
        $this->fallback_locale->delete();

        $this->assertTrue(Locale::all()->isEmpty());

        $this->artisan('i18n:install')->run();

        $this->assertNotNull(Locale::getFallbackLocale());
    }

    public function test_install_with_no_publish_migrations_option_should_not_publish_migrations()
    {
        $this->artisan('i18n:install --publish-migrations=false');

        $this->assertEquals(0, count($this->filesystem->files(database_path('migrations'))));
    }
}