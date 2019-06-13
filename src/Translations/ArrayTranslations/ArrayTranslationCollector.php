<?php


namespace Kodilab\LaravelI18n\Translations\ArrayTranslations;


use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Translation;

class ArrayTranslationCollector
{
    /** @var array */
    protected $array_translation_collection;

    /** @var Locale */
    protected $fallback_locale;

    /** @var Filesystem */
    protected $filesystem;

    protected $array_translations_fallback_iso;

    protected $array_translations_fallback_reference;

    protected $array_translations_en;

    public function __construct()
    {
        $this->filesystem = new Filesystem();
        $this->array_translation_collection = $this->discoverArrayTranslations();

        /** @var ArrayTranslations $array_translations */
        foreach ($this->array_translation_collection as $array_translations) {

            if ($array_translations->locale_reference() === Locale::getFallbackLocale()->iso) {
                $this->array_translations_fallback_iso = $array_translations;
            }

            if ($array_translations->locale_reference() === Locale::getFallbackLocale()->reference) {
                $this->array_translations_fallback_reference = $array_translations;
            }

            if ($array_translations->locale_reference() === 'en') {
                $this->array_translations_en = $array_translations;
            }
        }
    }

    public function collect()
    {
        $translations = [];
        /*
         * In order to return a list of translations, the process is described here:
         *
         * 1) Add all the paths retrieved by each array_translation
         * 2) Only keep the translation for each path from the fallback_locale translation. If it does not exists, from
         * the 'en' array translation. If it does not exists, then use the path
         */

        /** @var ArrayTranslations $array_translations */
        foreach ($this->array_translation_collection as $array_translations) {

            foreach ($array_translations->translations() as $path => $translation) {

                if (!isset($translations[$path])) {
                    $translations[$path] = new Translation($path, $this->bestTranslation($path));
                }

            }
        }

        return $translations;
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

    private function bestTranslation(string $path) {

        if (!is_null($this->array_translations_fallback_reference) && !is_null($this->array_translations_fallback_reference->find($path))) {
            return $this->array_translations_fallback_reference->find($path);
        }

        if (!is_null($this->array_translations_fallback_iso) && !is_null($this->array_translations_fallback_iso->find($path))) {
            return $this->array_translations_fallback_iso->find($path);
        }

        if (!is_null($this->array_translations_en) && !is_null($this->array_translations_en->find($path))) {
            return $this->array_translations_en->find($path);
        }

        return $path;
    }
}