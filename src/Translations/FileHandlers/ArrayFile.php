<?php


namespace Kodilab\LaravelI18n\Translations\FileHandlers;


class ArrayFile
{
    /** @var string */
    protected $path;

    /** @var string */
    protected $file_name;

    /** @var array */
    protected $translations;

    public function __construct(string $path)
    {
        $this->path = $path;
        $this->file_name = basename($path, '.php');

        $this->translations = $this->getTranslations($this->path);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    private function getTranslations(string $path)
    {
        $raw_content = [];

        if (file_exists($this->path)) {
            $raw_content = require $this->path;
        }

        $translations = $this->exportArrayKeys($raw_content);

        return $translations;
    }

    private function exportArrayKeys(array $raw)
    {
        return exportToPlainTranslationArray($this->file_name, $raw);
    }
}