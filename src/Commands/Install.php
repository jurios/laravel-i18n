<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\DetectsApplicationNamespace;
use Illuminate\Support\Facades\Schema;
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
    protected $signature = 'i18n:install';

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

        $this->output->title('Migrating database');

        $this->call('migrate');

        if (!$this->checkMigrationApplied()) {
            return -1;
        }
        $this->output->success('Migrations applied successfully');

        $this->output->title('Start sync process');
        $this->call('i18n:sync');
        $this->output->success('Sync completed');

        $this->output->title('Registering i18n Service Provider');
        $this->registerServiceProvider();
        $this->output->success('i18n Service Provider registered successfully');

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

    protected function checkMigrationApplied()
    {
        if (!Schema::hasTable(config('i18n.tables.locales', 'locales'))) {
            $this->output->error("\"".config('i18n.tables.locales', 'locales') . "\" table not found. " .
                "Did you published the migrations with \"php artisan i18n:migrations\"?");

            return false;
        }

        return true;
    }
}
