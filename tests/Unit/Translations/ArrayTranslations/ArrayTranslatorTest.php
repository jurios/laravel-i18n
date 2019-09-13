<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations\ArrayTranslations;


use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslator;

class ArrayTranslatorTest extends TestCase
{
    public function test_paths_contains_locale_directory_path_if_it_exists()
    {
        $locale = factory(Locale::class)->create();

        $translator = new ArrayTranslator($locale);

        $this->assertEquals([], $translator->paths);

        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $locale->iso);

        $translator = new ArrayTranslator($locale);

        $this->assertEquals([$this->lang_path . DIRECTORY_SEPARATOR . $locale->iso], $translator->paths);
    }

    public function test_path_contains_region_specific_directory_if_it_exists()
    {
        $locale = factory(Locale::class)->create();

        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $locale->iso);
        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $locale->reference);

        $translator = new ArrayTranslator($locale);

        $this->assertEquals([
            $this->lang_path . DIRECTORY_SEPARATOR . $locale->iso,
            $this->lang_path . DIRECTORY_SEPARATOR . $locale->reference,
            ], $translator->paths);
    }

    public function test_translations_contains_the_translations_from_all_files()
    {
        $locale = factory(Locale::class)->create();
        $filesystem = new Filesystem();

        $filesystem->copyDirectory(resource_path('lang/en'), $this->lang_path . DIRECTORY_SEPARATOR . $locale->reference);

        $translator = new ArrayTranslator($locale);

        $this->assertFalse($translator->translations->isEmpty());
    }

    public function test_region_scoped_translation_should_override_the_general_translation()
    {
        $general_locale = factory(Locale::class)->create(['region' => null]);
        $specific_locale = factory(Locale::class)->create(['iso' => $general_locale->iso]);

        $scope = $this->faker->unique()->word;
        $original = $this->faker->unique()->paragraph;
        $general_translation = $this->faker->unique()->paragraph;
        $specific_translation = $this->faker->unique()->paragraph;

        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $general_locale->iso);
        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $specific_locale->reference);

        $this->printTranslationFile(
            $this->lang_path
            . DIRECTORY_SEPARATOR
            . $general_locale->iso
            . DIRECTORY_SEPARATOR
            . $scope
            .'.php', [$original => $general_translation], 'array'
        );

        $this->printTranslationFile(
            $this->lang_path
            . DIRECTORY_SEPARATOR
            . $specific_locale->reference
            . DIRECTORY_SEPARATOR
            . $scope
            .'.php', [$original => $specific_translation], 'array'
        );

        $general_translator = new ArrayTranslator($general_locale);
        $specific_translator = new ArrayTranslator($specific_locale);

        $this->assertEquals($general_translation, $general_translator->translations[$scope . "." . $original]->translation);
        $this->assertEquals($specific_translation, $specific_translator->translations[$scope . "." . $original]->translation);

    }

    public function test_find_will_returns_the_translation()
    {
        $locale = factory(Locale::class)->create();

        $scope = $this->faker->unique()->word;
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        mkdir($this->lang_path . DIRECTORY_SEPARATOR . $locale->iso);

        $this->printTranslationFile(
            $this->lang_path
            . DIRECTORY_SEPARATOR
            . $locale->iso
            . DIRECTORY_SEPARATOR
            . $scope
            .'.php', [$original => $translation], 'array'
        );

        $translator = new ArrayTranslator($locale);

        $result = $translator->find($scope . "." . $original);

        $this->assertEquals($translation, $result->translation);
        $this->assertEquals($scope . "." . $original, $result->original);
    }
}