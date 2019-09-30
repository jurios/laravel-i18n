<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;
use Symfony\Component\Finder\SplFileInfo;

class CreateLocaleTest extends TestCase
{
    use WithFaker,
        InstallPackage;

    public function test_generates_a_locale()
    {
        $locale = factory(Locale::class)->make();

        $this->artisan('make:locale', [
            'reference' => $locale->reference
        ])->run();

        $this->assertNotNull(Locale::getLocale($locale->reference));
    }

    public function test_generate_a_fallback_locale()
    {
        $locale = factory(Locale::class)->make();

        $this->artisan('make:locale', [
            'reference' => $locale->reference,
            '--fallback' => true
        ])->run();

        $this->assertTrue(Locale::getLocale($locale->reference)->isFallback());
    }

    public function test_should_throw_an_exception_if_the_reference_is_not_valid()
    {
        $this->expectException(\InvalidArgumentException::class);

        $reference = 'EN_gb';

        $this->artisan('make:locale', [
            '--reference' => $reference
        ]);
    }
}