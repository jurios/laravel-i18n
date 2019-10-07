<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers\Contracts;


use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;

interface WriteTranslations
{
    /**
     * Save the translation collection into the file
     *
     * @param TranslationCollection $translations
     * @return mixed
     */
    public function save(TranslationCollection $translations);
}