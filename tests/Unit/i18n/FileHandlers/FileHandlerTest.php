<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n\FileHandlers;


use Kodilab\LaravelI18n\i18n\FileHandlers\PHPHandler;
use Kodilab\LaravelI18n\Tests\TestCase;

class FileHandlerTest extends TestCase
{
    public function test_filename_should_return_the_filename_without_extension()
    {
        $this->assertEquals('validation',
            (new PHPHandler(resource_path('lang/en/validation.php')))->getFilename()
        );
    }
}