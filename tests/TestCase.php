<?php

namespace Kodilab\LaravelI18n\Tests;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $test_files_path;

    protected $factories_path;

    protected $test_model_name = 'test_model';

    protected $test_model_table = 'test_models';

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->artisan('migrate')->run();

        $this->test_files_path = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'test_files';

        $this->generateTestFilesDirectory();

        $this->beforeApplicationDestroyed(function () {
            $this->destroyTestFilesDirectory();
        });

        $this->loadMigrationsFrom(__DIR__ . DIRECTORY_SEPARATOR . 'migrations');

        $this->factories_path = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'database/factories';
        $this->withFactories($this->factories_path);
    }

    protected function getPackageProviders($app)
    {
        return [
            \Kodilab\LaravelI18n\I18nProvider::class
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $config = include 'config/config.php';

        $app['config']->set('i18n', $config);
    }

    protected function generateTestFilesDirectory()
    {
        if (!file_exists($this->test_files_path))
        {
            mkdir($this->test_files_path);
        }
    }

    protected function destroyTestFilesDirectory()
    {
        if (file_exists($this->test_files_path))
        {
            try
            {
                rmdir($this->test_files_path);
            }
            catch (\Exception $e)
            {
                return;
            }
        }
    }

    protected function generateTestFile(string $file_name, string $content)
    {
        $file_path = $this->filePath($file_name);

        $result = file_put_contents($file_path, $content);

        $this->beforeApplicationDestroyed(function () use ($file_path) {
            unlink($file_path);
            $this->destroyTestFilesDirectory();
        });

        return $result !== false;
    }

    protected function filePath(string $file_name)
    {
        return $this->test_files_path . DIRECTORY_SEPARATOR . $file_name;
    }

    public function test_test_migration_is_fired()
    {
        $this->assertTrue(Schema::hasTable('test_models'));
    }
}
