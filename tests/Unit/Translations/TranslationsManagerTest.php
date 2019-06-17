<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations;



use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Translation;
use Kodilab\LaravelI18n\Translations\TranslationsManager;

class TranslationsManagerTest extends TestCase
{
    public function test_translations_is_the_translation_collection()
    {
        $count = $this->faker->numberBetween(0, 20);

        $locale = factory(Locale::class)->create();

        $this->generateRandomTranslationFile($locale, $count);

        $manager = new TranslationsManager($locale);

        $this->assertEquals(Collection::class, get_class($manager->translations));
        $this->assertEquals($count, count($manager->translations));
    }

    public function test_add_will_add_the_translation_into_the_collection()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $this->assertEquals(0, count($manager->translations));

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);

        $this->assertEquals(1, count($manager->translations));
        $this->assertEquals($original, $manager->translations->first()->original);
        $this->assertEquals($translation, $manager->translations->first()->translation);

    }

    public function test_add_will_persist_the_translation_into_the_file()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        //We assure the translation file is created
        $this->generateRandomTranslationFile($locale);

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);


        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertTrue(key_exists($original, $file_content_array));
        $this->assertEquals($file_content_array[$original], $translation);
    }

    public function test_add_will_generate_the_translation_file_if_does_not_exist()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $this->assertFalse(file_exists($manager->json_path));

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);

        $this->assertTrue(file_exists($manager->json_path));
    }

    public function test_add_will_replace_the_translation_if_it_exists()
    {

        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        //We assure the translation file is created
        $this->generateRandomTranslationFile($locale);

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;
        $translation2 = $this->faker->paragraph;

        $manager->add($original, $translation);

        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertEquals($file_content_array[$original], $translation);

        $manager->add($original, $translation2);

        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertEquals($file_content_array[$original], $translation2);
        $this->assertEquals($manager->find($original)->translation, $translation2);

    }

    public function test_delete_translation_will_delete_the_translation_from_the_collection()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $this->assertEquals(0, count($manager->translations));

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);

        $this->assertEquals(1, count($manager->translations));

        $manager->delete($original);

        $this->assertEquals(0, count($manager->translations));
    }

    public function test_delete_translation_will_delete_the_transtion_from_the_file()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        //We assure the translation file is created
        $this->generateRandomTranslationFile($locale);

        $manager->refresh();

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);


        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertTrue(key_exists($original, $file_content_array));

        $manager->delete($original);

        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertFalse(key_exists($original, $file_content_array));

    }

    public function test_delete_translation_will_remove_the_file_if_the_collection_is_empty()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);

        $this->assertTrue(file_exists($manager->json_path));

        $manager->delete($original);

        $this->assertFalse(file_exists($manager->json_path));
    }

    public function test_refresh_will_load_the_translations_from_the_file()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $count = $this->faker->numberBetween(0, 100);

        //We assure the translation file is created
        $this->generateRandomTranslationFile($locale, $count);

        $this->assertEquals(0, count($manager->translations));

        $manager->refresh();

        $this->assertEquals($count, count($manager->translations));
    }

    public function test_find_will_return_the_translation_of_original()
    {
        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $original = $this->faker->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);

        $occurrence = $manager->find($original);

        $this->assertEquals($translation, $occurrence->translation);
    }

    public function test_percentage_returns_the_percentage_translated()
    {
        $fallback_manager = new TranslationsManager($this->fallback_locale);

        $translation = new Translation($this->faker->paragraph, $this->faker->paragraph);

        $this->generateRandomTranslationFile($this->fallback_locale, 1);
        $fallback_manager->refresh();
        $fallback_manager->add($translation->original, $translation->translation);

        $this->assertEquals(100, $fallback_manager->percentage);


        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $manager->add($translation->original, $translation->translation);

        $this->assertEquals(50, $manager->percentage);
    }
}