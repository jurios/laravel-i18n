<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers;


use Kodilab\LaravelI18n\i18n\FileHandlers\Contracts\ReadTranslations;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Support\Arr;

class PHPHandler extends FileHandler implements ReadTranslations
{
    public function __construct(string $path)
    {
        parent::__construct($path, self::PHP_EXTENSION);
    }

    public function getTranslations(): TranslationCollection
    {
        $translations = new TranslationCollection();

        if (!file_exists($this->path)) {
            return $translations;
        }

        $raw = require $this->path;
        $content = Arr::dot($raw, $this->filename . '.');

        foreach ($content as $path => $translation) {
            // If is an empty array, Arr::dot() returns the empty array, thus we should ignore it
            if (is_string($translation)) {
                $translations->add(new Translation($path, $translation));
            }
        }

        return $translations;
    }
}