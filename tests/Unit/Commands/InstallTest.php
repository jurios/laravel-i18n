<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;
use Kodilab\LaravelI18n\Tests\Traits\MigratePackage;

class InstallTest extends TestCase
{
    use InstallPackage;

    public function test_fallback_locale_is_created()
    {
        $this->assertNotNull(Locale::getFallbackLocale());
    }
}