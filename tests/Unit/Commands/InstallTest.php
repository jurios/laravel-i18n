<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class InstallTest extends TestCase
{
    public function test_install_should_generate_a_migration_file()
    {
        $this->assertEquals(0, count($this->filesystem->files(database_path('migrations'))));

        $this->artisan('i18n:install', [
            '--fallback' => factory(Locale::class)->make()->reference
        ])->run();

        $this->assertEquals(1, count($this->filesystem->files(database_path('migrations'))));
    }

    public function test_fallback_locale_is_created()
    {
        $reference = factory(Locale::class)->make()->reference;

        $this->artisan('i18n:install', [
            '--fallback' => $reference
        ])->run();

        $this->assertNotNull(Locale::getLocale($reference));
    }

    public function test_install_with_no_publish_migrations_option_should_not_publish_migrations()
    {
        $this->artisan('i18n:install', [
            '--fallback' => factory(Locale::class)->make()->reference,
            '--publish-migrations' => false
        ]);

        $this->assertEquals(0, count($this->filesystem->files(database_path('migrations'))));
    }
}