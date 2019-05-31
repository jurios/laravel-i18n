<?php


namespace Kodilab\LaravelI18n\Tests\Unit;



use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslationFile;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslations;
use Symfony\Component\Finder\SplFileInfo;

class ArrayTranslationsTest extends TestCase
{
    public function test_directory_returns_the_path()
    {
        $languageCode = $this->faker->unique()->word;

        $this->copyLangFolder($languageCode);

        $at = new ArrayTranslations($this->lang_path . DIRECTORY_SEPARATOR . $languageCode);

        $this->assertEquals($this->lang_path . DIRECTORY_SEPARATOR . $languageCode, $at->directory());
    }

    public function test_path_files_returns_all_file_paths()
    {
        $languageCode = $this->faker->unique()->word;

        $this->copyLangFolder($languageCode);

        $at = new ArrayTranslations($this->lang_path . DIRECTORY_SEPARATOR . $languageCode);

        $paths = [];

        /** @var SplFileInfo $file */
        foreach ($this->filesystem->files($this->lang_path . DIRECTORY_SEPARATOR . $languageCode) as $file) {
            $paths[] = $file->getFilename();
        }

        $this->assertEquals($paths, $at->filePaths());
    }

    public function test_path_files_ignores_no_php_files()
    {
        $languageCode = $this->faker->unique()->word;

        $ignored_file_name = $this->faker->word;

        $this->copyLangFolder($languageCode);

        file_put_contents($this->lang_path . DIRECTORY_SEPARATOR . $languageCode . DIRECTORY_SEPARATOR . $ignored_file_name, "");

        $at = new ArrayTranslations($this->lang_path . DIRECTORY_SEPARATOR . $languageCode);

        $paths = [];

        /** @var SplFileInfo $file */
        foreach ($this->filesystem->files($this->lang_path . DIRECTORY_SEPARATOR . $languageCode) as $file) {
            $paths[] = $file->getFilename();
        }

        $this->assertNotEquals($paths, $at->filePaths());
        $this->assertFalse(in_array($ignored_file_name, $at->filePaths()));
    }

    public function test_translations_returns_plain_array_of_all_translations()
    {
        $languageCode = $this->faker->unique()->word;

        $this->copyLangFolder($languageCode);

        $at = new ArrayTranslations($this->lang_path . DIRECTORY_SEPARATOR . $languageCode);

        $this->assertIsArray($at->translations());
    }

    private function copyLangFolder(string $localeCode = 'en')
    {
        $this->filesystem->copyDirectory(
            resource_path('lang/en'),
            $this->lang_path . DIRECTORY_SEPARATOR . $localeCode
        );
    }
}