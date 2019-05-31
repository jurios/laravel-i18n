<?php


namespace Kodilab\LaravelI18n\Tests\Unit;



use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslationFile;

class ArrayTranslationFileTest extends TestCase
{
    public function test_name_attribute_is_the_scope_name_of_the_file()
    {
        $this->copyLangFolder($this->fallback_locale->reference);

        $atf = new ArrayTranslationFile(
            $this->resources_path .
            DIRECTORY_SEPARATOR .
            'lang' .
            DIRECTORY_SEPARATOR .
            $this->fallback_locale->reference .
            DIRECTORY_SEPARATOR .
            'validation.php'
        );


        $this->assertEquals('validation', $atf->name());

    }

    public function test_content_contains_the_content_array()
    {
        $this->copyLangFolder($this->fallback_locale->reference);

        $atf = new ArrayTranslationFile(
            $this->resources_path .
            DIRECTORY_SEPARATOR .
            'lang' .
            DIRECTORY_SEPARATOR .
            $this->fallback_locale->reference .
            DIRECTORY_SEPARATOR .
            'validation.php'
        );

        $content = require
            $this->resources_path .
            DIRECTORY_SEPARATOR .
            'lang' .
            DIRECTORY_SEPARATOR .
            $this->fallback_locale->reference .
            DIRECTORY_SEPARATOR .
            'validation.php';


        $this->assertEquals($content, $atf->rawContent());
    }

    public function test_export_will_export_the_content_array()
    {
        $this->copyLangFolder($this->fallback_locale->reference);

        $atf = new ArrayTranslationFile(
            $this->resources_path .
            DIRECTORY_SEPARATOR .
            'lang' .
            DIRECTORY_SEPARATOR .
            $this->fallback_locale->reference .
            DIRECTORY_SEPARATOR .
            'validation.php'
        );

        $content = require
            $this->resources_path .
            DIRECTORY_SEPARATOR .
            'lang' .
            DIRECTORY_SEPARATOR .
            $this->fallback_locale->reference .
            DIRECTORY_SEPARATOR .
            'validation.php';

        $this->assertEquals(exportToPlainTranslationArray('validation', $content), $atf->export());
    }

    private function copyLangFolder(string $localeCode = 'en')
    {
        $this->filesystem->copyDirectory(
            resource_path('lang/en'),
            $this->lang_path . DIRECTORY_SEPARATOR . $localeCode
        );
    }
}