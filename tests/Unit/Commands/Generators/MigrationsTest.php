<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class MigrationsTest extends TestCase
{
    use WithFaker;

    public function test_generates_migration_file()
    {
        $files = $this->filesystem->files(__DIR__ . '/../../../../src/Commands/Generators/stubs/Migrations');

        $this->artisan('i18n:migrations')->run();

        $this->assertEquals(
            count($files),
            count($this->filesystem->files(database_path('migrations')))
        );
    }

    public function test_config_generates_a_replace_config_file_if_force_flag()
    {
        $this->artisan('i18n:migrations')->run();

        /** @var SplFileInfo $migration */
        $migration = $this->filesystem->files(database_path('migrations'))[0];

        $content = $this->faker->paragraph;

        file_put_contents($migration->getRealPath(), $content);

        $this->assertEquals($content, file_get_contents($migration->getRealPath()));

        $this->artisan('i18n:migrations', ['--force' => true])->run();

        $this->assertNotEquals($content, file_get_contents($migration->getRealPath()));
    }
}