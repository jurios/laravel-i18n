<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\MigratePackage;

class InstallTest extends TestCase
{
    use MigratePackage;

    public function test_fallback_locale_is_created()
    {
        $reference = factory(Locale::class)->make()->reference;

        $this->artisan('i18n:install', [
            '--publish-migrations' => false,
            '--fallback' => $reference
        ])->run();

        $this->assertNotNull(Locale::getLocale($reference));
    }

    public function test_install_with_no_publish_migrations_option_should_not_publish_migrations()
    {
        $migrations = $this->filesystem->files(database_path('migrations'));

        $this->artisan('i18n:install', [
            '--fallback' => factory(Locale::class)->make()->reference,
            '--publish-migrations' => false
        ]);

        $this->assertEquals(count($migrations), count($this->filesystem->files(database_path('migrations'))));
    }
}