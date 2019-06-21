<?php


namespace Kodilab\LaravelI18n\Translations;


use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\ArrayTranslations\ArrayTranslator;
use Kodilab\LaravelI18n\Translations\FileHandlers\JsonFile;

class Translator
{
    /**
     * Translator locale
     *
     * @var Locale
     */
    protected $locale;

    /**
     * @var Collection
     */
    protected $translations;

    /**
     * Translator constructor.
     * @param Locale $locale
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->translations = $this->getTranslations();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Finds a translation
     *
     * @param string $original
     * @return mixed
     */
    public function find(string $original)
    {
        return $this->translations->where('original', $original)->first();
    }

    /**
     * Sync the translations with the originals
     *
     * @param array $originals
     */
    public function sync(array $originals)
    {
        $new_translations = new Collection();

        foreach ($originals as $original) {

            $translation = $this->getExistingTranslationText($original, true);

            $new_translations->put($original, new Translation($original, $translation));
        }

        $this->translations = $new_translations;
    }

    /**
     * Returns the translated text in case the original has been already translated in the json file or in
     * the locale array files
     *
     * @param string $original
     * @param bool $lookArrays
     * @return |null
     */
    private function getExistingTranslationText(string $original, bool $lookArrays = false)
    {
        if (!is_null($occurrence = $this->find($original))) {
            return $occurrence->translation;
        }

        $arrayTranslator = new ArrayTranslator($this->locale);

        $translation = !is_null($occurrence = $arrayTranslator->find($original)) ? $occurrence->translation : null;

        if (is_null($translation) && $this->locale->isFallback()) {
            return $original;
        }

        return $translation;
    }

    /**
     * Returns the translations (joined the fallback translations)
     *
     * @return Collection
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    private function getTranslations()
    {
        $path = $path = config('i18n.lang_path', resource_path('lang'))
            . DIRECTORY_SEPARATOR
            . $this->locale->reference
            . '.json';

        $result = new Collection();
        $handler = new JsonFile($path);
        $translations = $handler->translations;


        if (!$this->locale->isFallback()) {
            $result = $this->getFallbackTranslationsAsEmpty();
        }

        foreach ($translations as $original => $translation) {
            $result->put($original, new Translation($original, $translation));
        }

        return $result;
    }

    /**
     * Returns the fallback translations as untranslated
     *
     * @return Collection
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    private function getFallbackTranslationsAsEmpty()
    {
        $fallback_locale = Locale::getFallbackLocale();

        $translator = new Translator($fallback_locale);

        $result = $translator->translations;

        foreach ($result as $translation) {
            $translation->translation = null;
        }

        return $result;
    }
}