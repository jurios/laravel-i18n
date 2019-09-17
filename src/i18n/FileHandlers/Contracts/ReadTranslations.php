<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers\Contracts;


use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;

interface ReadTranslations
{
    /**
     * Get the translations from the file
     *
     * @return TranslationCollection
     */
    public function getTranslations() : TranslationCollection;
}