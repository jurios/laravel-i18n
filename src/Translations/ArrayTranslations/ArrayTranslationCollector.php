<?php


namespace Kodilab\LaravelI18n\Translations\ArrayTranslations;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\Translation;

class ArrayTranslationCollector
{
    /** @var array */
    protected $array_translation_collection;

    /** @var Locale */
    protected $fallback_locale;

    protected $array_translations_fallback;

    protected $array_translations_en;

    public function __construct(array $array_translation_collection)
    {
        $this->array_translation_collection = $array_translation_collection;

        /** @var ArrayTranslations $array_translations */
        foreach ($this->array_translation_collection as $array_translations) {

            if ($array_translations->locale_reference() === Locale::getFallbackLocale()->reference) {
                $this->array_translations_fallback = $array_translations;
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

    private function bestTranslation(string $path) {
        if (!is_null($this->array_translations_fallback) && !is_null($this->array_translations_fallback->find($path))) {
            return $this->array_translations_fallback->find($path);
        }

        if (!is_null($this->array_translations_en) && !is_null($this->array_translations_en->find($path))) {
            return $this->array_translations_en->find($path);
        }

        return $path;
    }
}