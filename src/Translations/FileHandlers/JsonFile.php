<?php


namespace Kodilab\LaravelI18n\Translations\FileHandlers;


class JsonFile
{
    /** @var string */
    protected $path;

    public $content;

    public function __construct(string $path)
    {
        $this->path = $path;

        $this->content = $this->getContent($this->path);
    }

    public function __get($name)
    {
        if ($name === 'translations') { return $this->content; }
    }

    /**
     * Get the array content from the JSON file
     * @param string $path
     * @return array|mixed
     */
    private function getContent(string $path)
    {
        if (file_exists($path)) {
            $content = json_decode(file_get_contents($path), true);

            return $content;
        }

        return [];
    }


}