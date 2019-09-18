<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\i18n\Translations\TranslationManager;
use Kodilab\LaravelI18n\i18n\Linguist;
use Kodilab\LaravelI18n\Models\Locale;

class Sync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'i18n:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Syncronize laravel translations found in php files with fallback language translations on the database';

    /** @var Linguist */
    protected $linguist;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Linguist $linguist)
    {
        parent::__construct();
        $this->linguist = $linguist;
        $this->filesystem = new Filesystem();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     *
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function handle()
    {
        $paths = $this->getPaths();

        /** @var Locale $locale */
        foreach (Locale::all() as $locale) {
            $translator = new TranslationManager($locale);
            $translator->sync($paths);
        }
    }

    /**
     * Returns the paths to be synchronized
     * @return array
     */
    private function getPaths()
    {
        /** @var TranslationCollection $parsed_translations */
        $parsed_translations = $this->linguist->texts();

        /** @var TranslationCollection $php_translations */
        $php_translations = $this->getTranslationsFromPHPFiles();

        $translations = (new TranslationCollection($parsed_translations))->merge($php_translations);

        return $translations->pluck('path')->toArray();
    }

    /**
     * Get the php translations paths from all locales
     *
     * @return TranslationCollection
     */
    private function getTranslationsFromPHPFiles()
    {
        $result = new TranslationCollection();

        /** @var Locale $locale */
        foreach (Locale::all() as $locale) {
            $translations = (new TranslationManager($locale))->php();
            $result = $result->merge($translations);
        }

        return $result;
    }
}
