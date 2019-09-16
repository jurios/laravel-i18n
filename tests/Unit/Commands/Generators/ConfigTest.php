<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Commands\Generators;


use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;

class ConfigTest extends TestCase
{
    protected function tearDown(): void
    {
        $this->filesystem->delete(config_path('i18n.php'));

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test_config_generates_a_config_file()
    {
        $this->assertFalse($this->filesystem->exists(config_path('i18n.php')));

        $this->artisan('i18n:config')->run();

        $this->assertTrue($this->filesystem->exists(config_path('i18n.php')));
    }

    public function test_config_generates_a_replace_config_file_if_force_flag()
    {
        $this->artisan('i18n:config')->run();

        file_put_contents(config_path('i18n.php'), $this->faker->paragraph);

        $this->artisan('i18n:config', ['--force' => true])->run();

        $this->assertEquals(
            file_get_contents(__DIR__ . '/../../../../config/config.php'),
            file_get_contents(config_path('i18n.php'))
        );
    }
}