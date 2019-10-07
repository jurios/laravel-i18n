<?php


namespace Kodilab\LaravelI18n\i18n\Translations;


use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\Models\Locale;

class TranslationManager
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * @var string
     */
    protected $translation_path;

    /**
     * @var JSONHandler
     */
    protected $handler;

    /**
     * @var TranslationCollection
     */
    protected $translations;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->translation_path = resource_path('lang/' . $this->locale->reference . '.json');
        $this->refresh();
    }

    /**
     * Returns the translations
     *
     * @return TranslationCollection
     */
    public function getTranslations()
    {
        return $this->translations;
    }

    /**
     * Returns whether a translation exists and is not empty
     *
     * @param string $path
     * @return bool
     */
    public function isTranslated(string $path)
    {
        return $this->translations
            ->where('path', $path)
            ->where('translation', '<>', '')
            ->isNotEmpty();
    }

    public function getTranslation(string $path)
    {
        /** @var Translation $translation */
        $translation = $this->translations
            ->where('path', $path)->first();

        return !is_null($translation) ? $translation->translation : '';
    }

    public function setTranslation(string $path, string $translation)
    {
        if (!is_null($tr = $this->translations->where('path', $path)->first())) {
            $tr->translation = $translation;
        }

        $this->save();
    }

    public function save()
    {
        $this->handler->save($this->translations);
        $this->refresh();
    }

    /**
     * Refresh the file handler and the translations
     *
     */
    public function refresh()
    {
        $this->handler = new JSONHandler($this->translation_path);
        $this->translations = $this->handler->getTranslations();
    }
}