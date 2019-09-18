<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Translations\Collection;

class LocaleTest extends TestCase
{
    public function test_fallback_locale_can_not_be_desabled()
    {
        $this->fallback_locale->enabled = false;
        $this->fallback_locale->save();

        $this->assertTrue($this->fallback_locale->enabled);
    }

    public function test_locale_translation_will_returns_the_translations_collection()
    {
        $this->assertEquals(TranslationCollection::class, get_class($this->fallback_locale->translations));
    }

    public function test_getLocale_returns_the_locale_which_name_is_equal()
    {
        $locale = factory(Locale::class)->create();

        $result = Locale::getLocale($locale->name);

        $this->assertEquals($locale->id, $result->id);

        $locale = factory(Locale::class)->create(['region' => null]);

        $result = Locale::getLocale($locale->name);

        $this->assertEquals($locale->id, $result->id);
    }

    public function test_getLocaleOrFallback_should_return_the_locale_if_it_exists_or_the_fallback_locale()
    {
        $locale = factory(Locale::class)->create();

        $this->assertEquals(Locale::getLocale($locale->name), Locale::getLocaleOrFallback($locale->name));

        $this->assertTrue(Locale::getFallbackLocale()->is(Locale::getLocaleOrFallback('')));
    }

    public function test_locale_with_same_name_can_not_be_persisted()
    {
        $this->expectException(\Exception::class);

        $locale = factory(Locale::class)->create();

        factory(Locale::class)->create(['iso' => $locale->iso, 'region' => $locale->region]);
    }

    public function test_locale_with_same_name_and_empty_region_can_not_be_persisted()
    {
        $this->expectException(\Exception::class);

        $locale = factory(Locale::class)->create(['region' => null]);

        factory(Locale::class)->create(['iso' => $locale->iso, 'region' => null]);
    }

    public function test_locale_with_same_name_and_different_region_can_be_persisted()
    {
        $locale = factory(Locale::class)->create(['region' => null]);

        $locale2 = factory(Locale::class)->create(['iso' => $locale->iso]);

        $this->assertEquals($locale2->id, Locale::getLocale($locale2->name)->id);
    }
}