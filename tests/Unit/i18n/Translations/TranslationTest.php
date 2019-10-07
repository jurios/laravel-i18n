<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n\Translations;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\Tests\TestCase;

class TranslationTest extends TestCase
{
    use WithFaker;

    public function test_path_should_return_the_path()
    {
        $path = $this->faker->word . '.' . $this->faker->word;

        $translation = new Translation($path);

        $this->assertEquals($path, $translation->getPath());
    }

    public function test_translation_should_return_the_translation()
    {
        $path = $this->faker->word . '.' . $this->faker->word;
        $translation = $this->faker->paragraph;

        $object = new Translation($path, $translation);

        $this->assertEquals($translation, $object->getTranslation());
    }
}