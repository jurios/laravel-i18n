<?php


namespace Kodilab\LaravelI18n\Translations\FileManager;


use Illuminate\Support\Collection;

class FileManager
{

    /** @var string */
    protected $path;

    /** @var Collection */
    protected $translations;


    public function __construct(string $path)
    {
        $this->path = $path;
        $this->translations = [];
        $this->translations = $this->getTranslationsFromFile();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Returns the translations array from the file
     *
     * @return Collection
     */
    protected function getTranslationsFromFile()
    {
        return new Collection();
    }
}