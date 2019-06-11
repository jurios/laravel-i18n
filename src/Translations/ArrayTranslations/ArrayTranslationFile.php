<?php


namespace Kodilab\LaravelI18n\Translations\ArrayTranslations;


use Illuminate\Filesystem\Filesystem;

class ArrayTranslationFile
{
    /** @var string */
    protected $path;

    /** @var Filesystem */
    protected $filesystem;

    /** @var string */
    protected $name;

    /** @var array */
    protected $raw_content;

    public function __construct(string $path)
    {
        $this->filesystem = new Filesystem();

        $this->path = $path;
        $this->name = $this->getFilename($path);

        $this->raw_content = $this->getRawContent();
    }

    public function name()
    {
        return $this->name;
    }

    public function rawContent()
    {
        return $this->raw_content;
    }

    public function export()
    {
        return exportToPlainTranslationArray($this->name, $this->raw_content);
    }

    private function getRawContent()
    {
        if (file_exists($this->path)) {
            return require $this->path;
        }

        return [];
    }

    private function getFilename(string $path)
    {
        $splitted_path = explode(DIRECTORY_SEPARATOR, $this->path);

        $filename = $splitted_path[count($splitted_path) - 1];

        return str_replace('.php', '', $filename);
    }
}