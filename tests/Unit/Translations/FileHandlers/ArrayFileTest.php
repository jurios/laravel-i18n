<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Translations\FileHandlers;


use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;
use Kodilab\LaravelI18n\Translations\FileHandlers\ArrayFile;

class ArrayFileTest extends TestCase
{
    public function test_file_name_value_is_the_same_as_the_file_name_from_the_path()
    {
        $handler = new ArrayFile(resource_path('lang/en/validation.php'));

        $this->assertEquals('validation', $handler->file_name);
    }

    public function test_translation_are_being_exported()
    {
        $handler = new ArrayFile(resource_path('lang/en/validation.php'));

        foreach ($handler->translations as $path => $translation) {
            $this->assertEquals(1, preg_match('/validation\.\w+/', $path));
        }
    }
}