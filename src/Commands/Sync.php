<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Linguist;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslations;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslationCollector;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslator;
use Kodilab\LaravelI18n\Translations\Synchronizer;
use Kodilab\LaravelI18n\Translations\Translation;
use Kodilab\LaravelI18n\Translations\TranslationsManager;
use Kodilab\LaravelI18n\Translations\Translator;

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
        $originals = $this->getOriginals();

        /** @var Locale $locale */
        foreach (Locale::all() as $locale) {
            $translator = new Translator($locale);

            $translator->sync($originals);
        }
    }

    private function getOriginals()
    {
        $originals = [];
        $parsed_translations = $this->linguist->texts();

        /** @var Locale $locale */
        foreach (Locale::all() as $locale) {
            $array_translations = (new ArrayTranslator($locale))->translations;

            $translations = array_merge($parsed_translations, $array_translations->all());

            /** @var Translation $translation */
            foreach ($translations as $translation) {
                $originals[$translation->original] = $translation->original;
            }
        }

        return array_keys($originals);
    }
}
