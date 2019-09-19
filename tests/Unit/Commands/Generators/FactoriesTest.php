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

        $this->artisan('i18n:factories')->run();

        $this->assertEquals(
            count($files),
            count($this->filesystem->files(database_path('factories')))
        );
    }

    public function test_config_generates_a_replace_factories_if_force_flag()
    {
        $this->artisan('i18n:factories')->run();

        /** @var SplFileInfo $factory */
        $factory = $this->filesystem->files(database_path('factories'))[0];

        $content = $this->faker->paragraph;

        file_put_contents($factory->getRealPath(), $content);

        $this->assertEquals($content, file_get_contents($factory->getRealPath()));

        $this->artisan('i18n:factories', ['--force' => true])->run();

        $this->assertNotEquals($content, file_get_contents($factory->getRealPath()));
    }
}