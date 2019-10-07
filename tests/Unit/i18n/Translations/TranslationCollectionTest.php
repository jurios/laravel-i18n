<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n\Translations;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Tests\TestCase;

class TranslationCollectionTest extends TestCase
{
    use WithFaker;

    public function test_where_path_should_return_the_translation_which_contains_the_path()
    {
        $translation = new Translation($this->faker->word, $this->faker->paragraph);

        $collection = new TranslationCollection([$translation]);

        $this->assertTrue($translation->is($collection->where('path', $translation->getPath())->first()));
    }
}