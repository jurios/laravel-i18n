<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations\FileHandlers;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
use Kodilab\LaravelI18n\Translations\FileHandlers\JsonFile;

class JsonTest extends TestCase
{
    public function test_content_is_equal_than_the_translation_defined_in_the_file()
    {
        $locale = factory(Locale::class)->create();
        $json_path = $this->lang_path . DIRECTORY_SEPARATOR . $locale->reference . '.json';

        $translations = [];

        for($i = 0; $i < 10; $i++) {
            $translations[$this->faker->paragraph] = $this->faker->paragraph;
        }

        $this->addTranslationsToFile($json_path, $translations);

        $jsonHandler = new JsonFile($json_path);

        $this->assertEquals($translations, $jsonHandler->content);
    }
}