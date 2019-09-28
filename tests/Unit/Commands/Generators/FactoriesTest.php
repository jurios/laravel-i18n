<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class FactoriesTest extends TestCase
{
    use WithFaker;

    public function test_generates_factory_files()
    {
        $files = $this->filesystem->files(__DIR__ . '/../../../../database/factories');

        $this->artisan('locale:factory')->run();

        $this->assertEquals(
            count($files),
            count($this->filesystem->files(database_path('factories')))
        );
    }
}