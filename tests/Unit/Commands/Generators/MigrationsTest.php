<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Tests\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class MigrationsTest extends TestCase
{
    use WithFaker;

    public function test_migrations_generates_migration_file()
    {
        $this->artisan('i18n:migrations', [
            '--filename' => 'test.php'
        ])->run();

        $this->assertTrue($this->filesystem->exists(database_path('migrations/test.php')));

        $this->filesystem->delete(database_path('migrations/test.php'));
    }
}