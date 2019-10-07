<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;
use Symfony\Component\Finder\SplFileInfo;

class RemoveLocaleTest extends TestCase
{
    use WithFaker,
        InstallPackage;

    public function test_removes_a_locale()
    {
        $locale = factory(Locale::class)->create();

        $this->artisan('locale:remove', [
            'reference' => $locale->reference
        ])->assertExitCode(0)->run();

        $this->assertNull(Locale::find($locale->id));
    }

    public function test_remove_using_a_reference_does_not_exists_an_exception_is_not_thrown()
    {
        $this->artisan('locale:remove', [
            'reference' => $this->faker->languageCode
        ])->assertExitCode(0);
    }

    public function test_remove_a_fallback_locale_should_not_remove_it()
    {
        $locale = Locale::getFallbackLocale();

        $this->artisan('locale:remove', [
            'reference' => $locale->reference
        ])->assertExitCode(0)->run();

        $this->assertNotNull(Locale::find($locale->id));
    }
}