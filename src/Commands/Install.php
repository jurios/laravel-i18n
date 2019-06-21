<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Installer;

class Install extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:i18n';

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
     *
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public function handle()
    {
        /** @var Locale $locale */
        $locale = null;

        $this->output->writeln('The project\'s fallback locale is ' . config('app.fallback_locale'));

        try {

            $locale = Locale::getFallbackLocale();
            $this->output->writeln("Fallback locale found.");

        } catch (MissingFallbackLocaleException $e) {

            $this->output->writeln("Fallback locale not created. Creating one by default based on the project's config.");

            Locale::create([
                'iso' => config('app.fallback_locale'),
                'region' => null,
                'description' => 'Fallback locale autogenerated by Laravel I18n install',
                'laravel_locale' => config('app.fallback_locale'),
                'currency_number_decimals' => null,
                'currency_decimals_punctuation' => null,
                'currency_thousands_separator' => null,
                'currency_symbol' => null,
                'currency_symbol_position' => 'before',
                'carbon_locale' => config('app.fallback_locale'),
                'carbon_tz' => null,
                'enabled' => true,
                'fallback' => true
            ]);
        }
    }
}
