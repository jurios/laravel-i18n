<?php


namespace Kodilab\LaravelI18n\Commands\Generators;


use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Builder\i18nBuilder;
use Kodilab\LaravelI18n\Facades\i18n;

class Locale extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:locale
                            {--reference= : Locale reference}
                            {--name= : Locale name}
                            {--fallback= : (true|false) Set the locale as fallback locale}
                            {--laravel-locale= : Laravel locale setting value}
                            {--carbon-locale= : Laravel locale setting value}
                            {--tz= : Locale timezone}
                            {--decimals= : Decimals when show float values}
                            {--decimals-punctuation= : Decimals when show localized float values}
                            {--thousands-separator= : Thousands separator when show localized values}
                            {--currency-symbol= : Currency symbol when show localized currency value}
                            {--currency_symbol_position= : Currency symbol position when show a localized currency value (after|before)}
                            {--hide-title= : Hide the command title}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates the a locale if it does not exists';

    /**
     * Execute the console command.
     *
     * @throws \Exception
     */
    public function handle()
    {
        if ($this->option('hide-title') !== true) {
            $this->output->title('Generating i18n locale');
        }

        try {
            i18nBuilder::createLocale([
                'language' => i18n::getLanguage($this->option('reference')),
                'region' => i18n::getRegion($this->option(('reference'))),
                'name' => $this->option('name'),
                'fallback' => $this->option('fallback')?: false,
                'laravel_locale' => $this->option('laravel-locale'),
                'carbon_locale' => $this->option('carbon-locale'),
                'tz' => $this->option('tz'),
                'decimals' => $this->option('decimals'),
                'decimals_punctuation' => $this->option('decimals-punctuation'),
                'thousands_separator' => $this->option('thousands-separator'),
                'currency_symbol' => $this->option('currency-symbol'),
                'currency_symbol_position' => $this->option('currency_symbol_position') !== null ?
                    $this->option('currency_symbol_position') : 'after',
            ]);

            $this->output->success('Fallback locale created: ' . $this->option('reference'));

        } catch (\Exception $exception) {
            $this->output->error('Locale can not be created: ' . $exception->getMessage());
            throw $exception;
        }
    }
}