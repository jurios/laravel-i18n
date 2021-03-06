<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Builder\i18nBuilder;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;

class LocaleTest extends TestCase
{
    use WithFaker, InstallPackage;

    public function test_getLocale_returns_the_locale_which_reference_is_equal()
    {
        $locale = factory(Locale::class)->create();

        $result = Locale::getLocale($locale->reference);

        $this->assertEquals($locale->id, $result->id);

        $locale = factory(Locale::class)->create(['region' => null]);

        $result = Locale::getLocale($locale->reference);

        $this->assertEquals($locale->id, $result->id);
    }

    public function test_getLocaleOrFallback_should_return_the_locale_if_it_exists_or_the_fallback_locale()
    {
        $locale = factory(Locale::class)->create();

        $this->assertEquals(Locale::getLocale($locale->reference), Locale::getLocaleOrFallback($locale->reference));

        $this->assertTrue(Locale::getFallbackLocale()->is(Locale::getLocaleOrFallback('')));
    }

    public function test_locale_with_same_reference_can_not_be_persisted()
    {
        $this->expectException(\Exception::class);

        $locale = factory(Locale::class)->create();

        factory(Locale::class)->create(['language' => $locale->language, 'region' => $locale->region]);
    }

    public function test_locale_with_same_language_and_region_null_can_not_be_persisted()
    {
        $this->expectException(\Exception::class);

        $locale = factory(Locale::class)->create(['region' => null]);

        factory(Locale::class)->create(['language' => $locale->language, 'region' => null]);
    }

    public function test_locale_with_same_language_and_different_region_can_be_persisted()
    {
        $locale = factory(Locale::class)->create(['region' => null]);

        $locale2 = factory(Locale::class)->create(['language' => $locale->language]);

        $this->assertEquals($locale2->id, Locale::getLocale($locale2->reference)->id);
    }

    public function test_name_should_be_the_locale_name_if_it_is_not_null()
    {
        $name = $this->faker->word;

        $locale = factory(Locale::class)->create(['name' => $name]);

        $this->assertEquals($name, $locale->name);
    }

    public function test_name_to_null_should_generate_a_name_equals_to_the_reference()
    {
        $locale = factory(Locale::class)->create(['name' => null]);

        $this->assertEquals($locale->reference, $locale->name);
    }

    public function test_update_name_to_null_should_generate_the_name_using_the_reference()
    {
        $locale = factory(Locale::class)->create();

        $this->assertNotEquals($locale->reference, $locale->name);

        $locale->update(['name' => null]);

        $this->assertEquals($locale->reference, $locale->name);
    }

    public function test_create_should_remove_the_fallback_flag_from_previous_fallback_locale_if_the_new_locale_fallback_is_true()
    {
        $locale = Locale::getFallbackLocale();

        $new_locale = factory(Locale::class)->create(['fallback' => true]);

        $this->assertFalse(Locale::find($locale->id)->fallback);
        $this->assertTrue(Locale::find($new_locale->id)->fallback);
    }

    public function test_create_should_keep_the_fallback_flag_if_the_new_locale_can_not_be_saved()
    {
        $fallback_locale = Locale::getFallbackLocale();

        $locale = factory(Locale::class)->make(['fallback' => true, 'language' => null]);

        try {
            $locale->save();
        } catch (\Exception $e) {}

        $this->assertTrue(Locale::find($fallback_locale->id)->fallback);
    }

    public function test_delete_should_not_remove_a_fallback_locale()
    {
        $this->expectException(\InvalidArgumentException::class);

        $fallback_locale = Locale::getFallbackLocale();

        $fallback_locale->delete();
    }
}