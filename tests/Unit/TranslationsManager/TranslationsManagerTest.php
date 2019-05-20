<?php


namespace Kodilab\LaravelI18n\Tests\Unit\TranslationsManager;



use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Models\Locale;
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
        $this->assertEquals($original, $manager->translations->first()['original']);
        $this->assertEquals($translation, $manager->translations->first()['translation']);

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

    public function test_import_array_translations_will_import_translations_into_the_json_file()
    {
        $index = $this->faker->word;
        $index2 = $this->faker->word;
        $index3 = $this->faker->word;
        $translation = $this->faker->paragraph;

        $array_translations = [
            $index => [
                $index2 => [
                    $index3 => $translation
                ]
            ]
        ];

        $locale = factory(Locale::class)->create();

        $manager = new TranslationsManager($locale);

        $manager->importArray($array_translations);

        $original = $index . '.'. $index2 . '.' . $index3;

        $this->assertEquals(1, count($manager->translations));
        $this->assertTrue($manager->translations->where('original', $original)->isNotEmpty());
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

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->paragraph;

        $manager->add($original, $translation);


        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertTrue(key_exists($original, $file_content_array));

        $manager->delete($original);

        $file_content_array = json_decode(file_get_contents($manager->json_path), true);

        $this->assertFalse(key_exists($original, $file_content_array));

    }
}