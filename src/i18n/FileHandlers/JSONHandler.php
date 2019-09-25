<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers;


use Kodilab\LaravelI18n\i18n\FileHandlers\Contracts\ReadTranslations;
use Kodilab\LaravelI18n\i18n\FileHandlers\Contracts\WriteTranslations;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;

class JSONHandler extends FileHandler implements ReadTranslations, WriteTranslations
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

    public function save(TranslationCollection $translations)
    {
        $json = json_encode($translations->sortBy('path')->toRaw(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($this->path, $json);
    }
}