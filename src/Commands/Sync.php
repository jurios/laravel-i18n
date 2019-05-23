<?php

namespace Kodilab\LaravelI18n\Commands;

use Illuminate\Console\Command;
use Kodilab\LaravelI18n\Linguist;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Synchronizer;
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

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Linguist $linguist)
    {
        parent::__construct();
        $this->linguist = $linguist;
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
        $texts = $this->linguist->texts();

        $locale = Locale::getFallbackLocale();

        $fallback_manager = new TranslationsManager($locale);
        $synchronizer = new Synchronizer($fallback_manager, array_keys($texts));

        //Only add new translations to the fallback locale as we guess is the original locale of the text
        $this->addNewTranslations($fallback_manager, $synchronizer->new());

        //For deprecated, they will be removed from all locale files
        $this->removeDeprecatedTranslations($synchronizer->deprecated());

    }

    /**
     * Add the new_translations originals to the manager locale's file
     *
     * @param TranslationsManager $manager
     * @param array $new_translations
     */
    private function addNewTranslations(TranslationsManager $manager, array $new_translations)
    {
        /** @var string $new_translation */
        foreach ($new_translations as $new_translation) {
            $manager->add($new_translation, $new_translation);
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
            $fallback_manager = new TranslationsManager($locale);

            /** @var string $deprecated */
            foreach ($deprecated_translations as $deprecated) {
                $fallback_manager->delete($deprecated);
            }
        }
    }
}
