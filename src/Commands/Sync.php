<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Linguist;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslations;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslationCollector;
use Kodilab\LaravelI18n\Translations\Synchronizer;
use Kodilab\LaravelI18n\Translations\Translation;
use Kodilab\LaravelI18n\Translations\TranslationsManager;

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
        $locale = Locale::getFallbackLocale();

        $array_translations = $this->discoverArrayTranslations();

        $at_sync = new ArrayTranslationCollector($array_translations);

        $array_translations = $at_sync->collect();

        $occurrence_translations = $this->linguist->texts();

        $translations = array_merge($array_translations, $occurrence_translations);

        $fallback_manager = new TranslationsManager($locale);
        $synchronizer = new Synchronizer($fallback_manager, $translations);

        //Only add new translations to the fallback locale as we guess is the original locale of the text
        $this->addNewTranslations($fallback_manager, $synchronizer->new());

        //For deprecated, they will be removed from all locale files
        $this->removeDeprecatedTranslations($synchronizer->deprecated());

    }


    private function discoverArrayTranslations()
    {
        $array_translations = [];

        $directories = $this->filesystem->directories(resource_path('lang'));

        /** @var string $directory */
        foreach ($directories as $directory) {
            $array_translations[] = new ArrayTranslations($directory);
        }

        return $array_translations;
    }

    /**
     * Add the new_translations originals to the manager locale's file
     *
     * @param TranslationsManager $manager
     * @param array $new_translations
     */
    private function addNewTranslations(TranslationsManager $manager, array $new_translations)
    {
        /** @var Translation $translation */
        foreach ($new_translations as $translation) {
            $manager->add($translation->original, $translation->translation);
        }
    }

    /**
     * Remove deprecated_translation from the all locale's file
     *
     * @param array $deprecated_translations
     */
    private function removeDeprecatedTranslations(array $deprecated_translations)
    {
        /** @var Locale $locale */
        foreach (Locale::all() as $locale) {
            $manager = new TranslationsManager($locale);

            /** @var Translation $deprecated */
            foreach ($deprecated_translations as $deprecated) {
                $manager->delete($deprecated->original);
            }
        }
    }
}
