<?php


namespace Kodilab\LaravelI18n\Translations\ArrayTranslations;


use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

class ArrayTranslations
{
    /** @var string */
    protected $locale_reference;

    /** @var string */
    protected $path;

    /** @var array */
    protected $file_paths;

    /** @var Filesystem */
    protected $filesystem;

    /** @var array */
    protected $translations;

    public function __construct(string $path)
    {
        $this->filesystem = new Filesystem();

        $this->path = $path;
        $this->locale_reference = $this->getLocaleReference($path);
        $this->file_paths = $this->discoverFiles();

        $this->collectPlainTranslations();
    }

    public function directory()
    {
        return $this->path;
    }

    public function filePaths()
    {
        return $this->file_paths;
    }

    public function translations()
    {
        return $this->translations;
    }

    public function find(string $path) {
        return isset($this->translations[$path]) ? $this->translations[$path] : null;
    }

    public function locale_reference()
    {
        return $this->locale_reference;
    }

    private function getLocaleReference(string $path)
    {
        $splitted_path = explode(DIRECTORY_SEPARATOR, $path);

        return $splitted_path[count($splitted_path) - 1];
    }

    private function collectPlainTranslations()
    {
        $this->translations = [];

        foreach ($this->file_paths as $relative_path) {
            $file_path = $this->path . DIRECTORY_SEPARATOR . $relative_path;

            $atf = new ArrayTranslationFile($file_path);

            $this->translations = array_merge($this->translations, $atf->export());
        }
    }

    private function discoverFiles()
    {
        $all_files = $this->filesystem->files($this->directory());

        $files = [];
        /** @var SplFileInfo $file */
        foreach ($all_files as $file) {
            if (preg_match('/[a-zA-Z0-9]\.php/', $file->getFilename())) {
                $files[] = $file->getRelativePathname();
            }
        }

        return $files;
    }
}