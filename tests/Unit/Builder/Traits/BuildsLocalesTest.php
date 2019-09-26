<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Builder\Traits;


use Illuminate\Support\Facades\DB;
use Kodilab\LaravelI18n\Builder\i18nBuilder;
use Kodilab\LaravelI18n\Exceptions\LocaleAlreadyExists;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class BuildsLocalesTest extends TestCase
{
    public function test_createLocale_creates_a_locale()
    {
        $data = factory(Locale::class)->make();

        i18nBuilder::createLocale($data->toArray());

        $this->assertNotNull(
            DB::table(config('i18n.tables.locales', 'locales'))
                ->where('language', $data->language)->where('region', $data->region)->get()->first()
        );
    }

    public function test_createLocale_should_throw_an_exception_if_a_locale_already_exists()
    {
        $this->expectException(LocaleAlreadyExists::class);

        $data = factory(Locale::class)->make();

        factory(Locale::class)->create($data->toArray());

        i18nBuilder::createLocale($data->toArray());
    }

    public function test_createLocale_should_remove_fallback_flag_to_previous_fallback_locale_if_the_new_locale_fallback_is_true()
    {
        $locale = Locale::getFallbackLocale();

        $data = factory(Locale::class)->make(['fallback' => true]);

        i18nBuilder::createLocale($data->toArray());

        $this->assertFalse(Locale::find($locale->id)->fallback);
    }

    public function test_createLocale_should_keep_the_fallback_flag_if_the_new_locale_can_not_be_saved()
    {
        $locale = Locale::getFallbackLocale();

        $data = factory(Locale::class)->make(['fallback' => true, 'language' => null]);

        try {
            i18nBuilder::createLocale($data->toArray());
        } catch (\Exception $e) {}

        $this->assertTrue(Locale::find($locale->id)->fallback);
    }

    public function test_removeLocale_should_remove_a_locale()
    {
        $locale = factory(Locale::class)->create();

        i18nBuilder::removeLocale($locale->reference);

        $this->assertNull(DB::table(config('i18n.tables.locales', 'locales'))->find($locale->id));
    }

    public function test_removeLocale_should_not_remove_fallback_locale()
    {
        $this->expectException(\RuntimeException::class);

        i18nBuilder::removeLocale(Locale::getFallbackLocale()->reference);
    }
}