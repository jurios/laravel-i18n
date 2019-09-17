<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers;


use Kodilab\LaravelI18n\i18n\FileHandlers\Contracts\ReadTranslations;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;

class JSONHandler extends FileHandler implements ReadTranslations
{
    public function __construct(string $path)
    {
        parent::__construct($path, self::JSON_EXTENSION);
    }

    public function getTranslations(): TranslationCollection
    {
        $translations = new TranslationCollection();

        if (file_exists($this->path)) {
            $content = json_decode(file_get_contents($this->path), true);

            foreach ($content as $path => $translation) {
                $translations->add(new Translation($path, $translation));
            }
        }

        return $translations;
    }
}