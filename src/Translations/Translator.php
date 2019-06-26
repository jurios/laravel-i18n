<?php


namespace Kodilab\LaravelI18n\Translations;


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
     * JSON file handler
     *
     * @var JsonFile
     */
    protected $handler;

    /**
     * Translator constructor.
     * @param Locale $locale
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->handler = new JsonFile($this->getJSONFilePath());
        $this->translations = $this->getTranslations();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($name === 'percentage') {
            return $this->getPercentage();
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

        $this->save();
    }

    /**
     * Save the translation collection into the file
     *
     */
    public function save()
    {
        $raw_translations = exportTranslationCollectionToRaw($this->translations);

        $this->handler->save($raw_translations);
    }

    /**
     * Refresh the translation collection from the file
     *
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function refresh()
    {
        $this->handler->refresh();
        $this->translations = $this->getTranslations();
    }

    /**
     * Updates a translation
     *
     * @param string $original
     * @param string $translation
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingFallbackLocaleException
     */
    public function update(string $original, string $translation)
    {
        if (isset($this->translations[$original])) {
            $this->translations[$original]->translation = $translation;
        }

        $this->save();
        $this->refresh();
    }

    private function getPercentage()
    {
        $not_null_translations_count = 0;

        foreach ($this->translations as $translation) {

            if (!$translation->isEmpty()) {
                $not_null_translations_count++;
            }
        }

        if (count($this->translations) === 0) {
            return 100;
        }

        $perc = ($not_null_translations_count * 100) / count($this->translations);

        return (int) $perc;

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
        $result = new Collection();

        $translations = $this->handler->translations;

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

    /**
     * Returns the JSON file path
     *
     * @return string
     */
    private function getJSONFilePath()
    {
        return config('i18n.lang_path', resource_path('lang'))
            . DIRECTORY_SEPARATOR
            . $this->locale->reference
            . '.json';
    }
}