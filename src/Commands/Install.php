<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Models\Locale;

class Install extends Command
{
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
        $this->publishMigrations();
        $this->generateDefaultFallbackLocale();
    }

    /**
     * Publish migration command
     */
    private function publishMigrations()
    {
        $this->call('i18n:generate', ['resource' => 'migrations']);
    }

    /**
     * Generates default fallback locale command
     */
    private function generateDefaultFallbackLocale()
    {
        $this->call('i18n:generate', ['resource' => 'fallback']);
    }
}
