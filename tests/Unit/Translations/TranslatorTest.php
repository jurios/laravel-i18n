<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Translation;
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

    public function test_translator_gets_the_null_translation_as_empty()
    {
        $locale = factory(Locale::class)->create();

        $original_text = $this->faker->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [$original_text => null]);

        $translator = new Translator($locale);

        $translation = $translator->translations[$original_text];

        $this->assertTrue($translation->isEmpty());
    }

    /*
     * TODO: This test is not working.
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

        $translator = new Translator($locale);

        $this->assertTrue(isset($translator->translations[$original_only_fallback]));
        $this->assertTrue($translator->translations[$original_only_fallback]->isEmpty());
    }*/

    public function test_sync_will_add_new_originals()
    {
        $locale = factory(Locale::class)->create();

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $this->faker->unique()->paragraph => $this->faker->unique()->paragraph
        ]);

        $translator = new Translator($locale);

        $original = $this->faker->unique()->paragraph;

        $translator->sync([$original]);

        $this->assertNotNull($translator->translations[$original]);
    }

    public function test_find_will_returns_the_translation()
    {
        $locale = factory(Locale::class)->create();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $original => $translation
        ]);

        $translator = new Translator($locale);

        $result = $translator->find($original);

        $this->assertEquals($translation, $result->translation);
        $this->assertEquals($original, $result->original);
    }

    public function test_sync_will_keep_the_existing_translations()
    {
        $locale = factory(Locale::class)->create();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $original => $translation
        ]);

        $translator = new Translator($locale);

        $translator->sync([$original]);

        $this->assertNotNull($translator->translations[$original]);
        $this->assertEquals($translation, $translator->translations[$original]->translation);
    }

    public function test_sync_will_remove_deprecated_translations()
    {
        $locale = factory(Locale::class)->create();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $original => $translation
        ]);

        $translator = new Translator($locale);

        $translator->sync([$this->faker->paragraph]);

        $this->assertFalse(isset($translator->translations[$original]));
    }

    public function test_sync_will_use_the_array_translation_if_it_exists()
    {
        $locale = factory(Locale::class)->create();

        $scope = $this->faker->unique()->word;
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $locale->iso);

        $this->addTranslationsToFile(
            $this->lang_path
            . DIRECTORY_SEPARATOR
            . $locale->iso
            . DIRECTORY_SEPARATOR
            . $scope
            .'.php', [$original => $translation], 'array'
        );

        $translator = new Translator($locale);

        $this->assertNull($translator->find($scope . "." . $original));

        $translator->sync([$scope . "." . $original]);

        $this->assertNotNull($translator->translations[$scope . "." . $original]);
        $this->assertEquals($translation, $translator->translations[$scope . "." . $original]->translation);
    }

    public function test_sync_will_use_original_as_translation_if_it_is_new_if_the_locale_is_fallback()
    {
        $locale = factory(Locale::class)->create();

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $this->faker->unique()->paragraph => $this->faker->unique()->paragraph
        ]);

        $this->addTranslationsToFile($this->getJSONPathFromLocale($this->fallback_locale), [
            $this->faker->unique()->paragraph => $this->faker->unique()->paragraph
        ]);

        $translator = new Translator($locale);

        $original = $this->faker->unique()->paragraph;

        $translator->sync([$original]);

        $this->assertNotNull($translator->translations[$original]);
        $this->assertNull($translator->translations[$original]->translation);

        $translator = new Translator($this->fallback_locale);

        $translator->sync([$original]);

        $this->assertNotNull($translator->translations[$original]);
        $this->assertEquals($original, $translator->translations[$original]->translation);
    }

    public function test_sync_will_save_changes_on_json_file()
    {
        $locale = factory(Locale::class)->create();

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $this->faker->unique()->paragraph => $this->faker->unique()->paragraph
        ]);

        $translator = new Translator($locale);

        $original = $this->faker->unique()->paragraph;

        $translator->sync([$original]);
        $translator->refresh();

        $this->assertNotNull($translator->find($original));
    }

    public function test_update_will_update_the_translation_collection()
    {
        $locale = factory(Locale::class)->create();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $original => $translation
        ]);

        $translator = new Translator($locale);

        $this->assertEquals($translation, $translator->find($original)->translation);

        $translation2 = $this->faker->unique()->paragraph;

        $translator->update($original, $translation2);

        $this->assertEquals($translation2, $translator->find($original)->translation);
    }

    public function test_update_will_save_the_changes_to_the_file()
    {
        $locale = factory(Locale::class)->create();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;
        $translation2 = $this->faker->unique()->paragraph;

        $this->addTranslationsToFile($this->getJSONPathFromLocale($locale), [
            $original => $translation
        ]);

        $translator = new Translator($locale);

        $translator->update($original, $translation2);
        $translator->refresh();

        $this->assertEquals($translation2, $translator->find($original)->translation);
    }
}