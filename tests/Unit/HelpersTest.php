<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Tests\TestCase;

class HelpersTest extends TestCase
{
    use WithFaker;

    public function test_exportToPlainTranslationArray_convert_array_multilevel_to_plain_array()
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

        $original = $index . '.'. $index2 . '.' . $index3;

        $this->assertEquals($translation, exportToPlainTranslationArray(null, $array_translations)[$original]);
    }
}