<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Translator;

class TranslatorTest extends TestCase
{
    public function test_translator_gets_the_locale_translation()
    {
        $locale = factory(Locale::class)->create();

        $original_text = $this->faker->paragraph;
        $translation_text = $this->faker->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [$original_text => $translation_text]);

        $translator = new Translator($locale);

        $translation = $translator->translations[$original_text];

        $this->assertEquals($original_text, $translation->original);
        $this->assertEquals($translation_text, $translation->translation);
    }

    public function test_translations_should_be_joined_with_fallback_translation_mark_those_translations_as_empty()
    {
        $locale = factory(Locale::class)->create();

        $original_only_fallback = $this->faker->unique()->paragraph;
        $translation_only_fallback = $this->faker->unique()->paragraph;

        $original = $this->faker->unique()->paragraph;
        $fallback_translation = $this->faker->unique()->paragraph;
        $locale_translation = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($this->fallback_locale), [
            $original_only_fallback => $translation_only_fallback,
            $original => $fallback_translation
        ]);

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
           $original => $locale_translation
        ]);

        $translator = new Translator($locale, $this->lang_path . DIRECTORY_SEPARATOR . $locale->reference . '.json');

        $this->assertTrue(isset($translator->translations[$original_only_fallback]));
        $this->assertTrue($translator->translations[$original_only_fallback]->isEmpty());
    }
}