<?php


namespace Kodilab\LaravelI18n\i18n\FileHandlers;


class FileHandler
{
    const PHP_EXTENSION = 'php';
    const JSON_EXTENSION = 'json';

    /**
     * @var string
     */
    protected $path;

    /**
     * @var string
     */
    protected $extension;

    /**
     * @var string
     */
    protected $filename;

    public function __construct(string $path, string $extension)
    {
        $this->path = $path;
        $this->extension = $extension;
        $this->filename = basename($this->path, '.' . $this->extension);
    }

    /**
     * Returns the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}