<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
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
        $this->assertEquals(Collection::class, get_class($this->fallback_locale->translations));
    }

    public function test_get_locale_returns_the_locale_which_reference_is_equal()
    {
        $locale = factory(Locale::class)->create();

        $result = Locale::getLocale($locale->reference);

        $this->assertEquals($locale->id, $result->id);

        $locale = factory(Locale::class)->create(['region' => null]);

        $result = Locale::getLocale($locale->reference);

        $this->assertEquals($locale->id, $result->id);
    }
}