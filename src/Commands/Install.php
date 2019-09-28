<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Providers\i18nServiceProvider;

class Install extends Command
{
    use DetectsApplicationNamespace;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:install
                            {--publish-migrations= : true|false Publish migrations before migrate }
                            {--fallback= : Fallback locale reference }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install laravel-i18n package';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->title('Installing laravel-i18n');

        if (is_null($this->option('publish-migrations')) || $this->option('publish-migrations')) {
            $this->publishMigrations();
        }

        $this->output->title('Migrating database');

        $this->call('migrate');

        $this->output->success('Migrations applied successfully');

        $this->generateFallbackLocale();

        $this->output->title('Start sync process');
        $this->call('i18n:sync');
        $this->output->success('Sync completed');

        $this->output->title('Registering i18n Service Provider');
        $this->registerServiceProvider();
        $this->output->success('i18n Service Provider registered successfully');

    }

    /**
     * Publish migration command
     */
    private function publishMigrations()
    {
        $this->call('i18n:migrations');
    }

    /**
     * Generates default fallback locale command
     */
    private function generateFallbackLocale()
    {
        $this->output->title('Generating fallback locale');

        if (is_null($reference = $this->option('fallback'))) {
            $reference = $this->ask('Fallback locale reference?(ex: en, en_GB', null);
        }

        $this->call('make:locale', [
            '--reference' => $reference,
            '--fallback' => true,
            '--hide-title' => true
        ]);
    }

    /**
     * Register the Telescope service provider in the application configuration file.
     *
     * @return void
     */
    protected function registerServiceProvider()
    {
        $namespace = Str::replaceLast('\\', '', $this->getAppNamespace());

        $appConfig = file_get_contents(config_path('app.php'));

        if (Str::contains($appConfig, i18nServiceProvider::class . '::class')) {
            return;
        }

        $lineEndingCount = [
            "\r\n" => substr_count($appConfig, "\r\n"),
            "\r" => substr_count($appConfig, "\r"),
            "\n" => substr_count($appConfig, "\n"),
        ];
        $eol = array_keys($lineEndingCount, max($lineEndingCount))[0];

        file_put_contents(config_path('app.php'), str_replace(
            "{$namespace}\\Providers\EventServiceProvider::class,".$eol,
            "{$namespace}\\Providers\EventServiceProvider::class,".$eol."        ". i18nServiceProvider::class ."::class,".$eol,
            $appConfig
        ));
    }
}
