<?php


namespace Kodilab\LaravelI18n\Tests\Traits;


use Kodilab\LaravelI18n\Models\Locale;

trait InstallPackage
{
    use MigratePackage;

    /**
     * Install the package
     *
     */
    public function installPackageSetUp()
    {
        $this->artisan('i18n:install', [
            '--publish-migrations' => false,
            '--fallback' => factory(Locale::class)->make()->reference,
        ]);

        $this->fallback_locale = Locale::getFallbackLocale();
    }
}