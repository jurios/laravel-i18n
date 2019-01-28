<?php

namespace Kodilab\LaravelI18n\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\I18n\I18n;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Models\Translation;
use Kodilab\LaravelI18n\Tests\TestCase;

class i18nTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var Locale */
    protected $fallback_locale;

    /** @var Locale */
    protected $user_locale;

    /** @var I18n */
    protected $i18n;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->fallback_locale = factory(Locale::class)->create([
            'fallback' => true,
            'enabled' => true
        ]);

        $this->user_locale = factory(Locale::class)->create([
            'enabled' => true
        ]);

        $this->i18n = new I18n();
    }

    public function test_translate_text_is_given_using_an_specific_locale()
    {
        $original_text = $this->generateOriginalTranslation($this->faker->text, $this->fallback_locale);

        /** @var Translation $translated_text */
        $translated_text = $this->generateTranslation($original_text, $this->user_locale);

        $result = $this->i18n->translate($original_text->translation, [], $this->fallback_locale);
        $this->assertEquals($original_text->translation, $result);

        $result = $this->i18n->translate($translated_text->translation, [], $this->user_locale);
        $this->assertEquals($translated_text->translation, $result);
    }

    public function test_translate_text_without_specific_locale_will_use_user_locale()
    {
        $this->session(['locale' => $this->user_locale]);

        $this->assertEquals(Locale::getUserLocale()->id, $this->user_locale->id);

        $original_text = $this->generateOriginalTranslation($this->faker->text, $this->fallback_locale);

        /** @var Translation $translated_text */
        $translated_text = $this->generateTranslation($original_text, $this->user_locale);

        $result = $this->i18n->translate($original_text->translation, []);

        $this->assertEquals($translated_text->translation, $result);
        $this->assertNotEquals($original_text->translation, $result);
    }

    public function test_translate_text_is_empty_if_translation_not_exists_and_is_honestly()
    {
        $original_text = $this->generateOriginalTranslation($this->faker->text, $this->fallback_locale);

        $result = $this->i18n->translate($original_text->translation, [], $this->user_locale);
        $this->assertEquals($original_text->translation, $result);

        $result = $this->i18n->translate($original_text->translation, [], $this->user_locale, true);
        $this->assertEquals("", $result);
    }

    private function generateOriginalTranslation(string $text, Locale $locale)
    {
        return factory(Translation::class)->create([
            'md5' => md5($text),
            'translation' => $text,
            'locale_id' => $locale->id
        ]);
    }

    private function generateTranslation(Translation $translation, Locale $locale, $text = null)
    {
        return factory(Translation::class)->create([
            'md5' => $translation->md5,
            'translation' => is_null($text) ? $this->faker->text : $text,
            'locale_id' => $locale->id
        ]);
    }
}