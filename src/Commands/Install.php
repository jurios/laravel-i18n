<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;

class Install extends Command
{
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
}
