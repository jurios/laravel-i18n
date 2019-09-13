<?php


namespace Kodilab\LaravelI18n\Translations\FileHandlers;


use Illuminate\Support\Arr;

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

    /**
     * Returns a "dot" notation array exported from the array translation file content
     *
     * @param string $path
     * @return array
     */
    private function getTranslations(string $path)
    {
        $raw_content = [];

        if (file_exists($path)) {
            $raw_content = require $path;
        }

        return Arr::dot($raw_content, $this->file_name . '.');
    }
}