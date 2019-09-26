<?php


namespace Kodilab\LaravelI18n\i18n\Sync;


use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\i18n\FileHandlers\PHPHandler;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Models\Locale;
use Symfony\Component\Finder\SplFileInfo;


class LocaleSync
{
    /**
     * @var Locale
     */
    protected $locale;

    /**
     * Path where the .php files are located
     *
     * @var string
     */
    protected $php_directory;

    /**
     * Path to JSON file
     *
     * @var string
     */
    protected $json_path;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string[]
     */
    protected $php_paths;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->filesystem = new Filesystem();
        $this->php_directory = resource_path('lang/' . $this->locale->reference);

        $this->json_path = resource_path('lang/' . $this->locale->reference . '.json');
        $this->php_paths = $this->getPHPFiles($this->php_directory);
    }

    /**
     * Returns the PHP file paths placed in the path
     *
     * @param string $path
     * @return array
     */
    private function getPHPFiles(string $path)
    {
        $php_files = [];

        if (is_dir($path)) {
            $php_files = array_filter($this->filesystem->files($path), function (SplFileInfo $file) {
                return $file->getExtension() === 'php';
            });
        }

       return array_map(function (SplFileInfo $file) {
           return $file->getRealPath();
       }, $php_files);
    }

    public function php()
    {
        $result = new TranslationCollection();

        /** @var string $file */
        foreach ($this->php_paths as $file) {
            $translations = (new PHPHandler($file))->getTranslations();

            /** @var Translation $translation */
            foreach ($translations as $translation) {
                $result->add($translation);
            }
        }

        return $result;
    }

    public function json()
    {
        return (new JSONHandler($this->json_path))->getTranslations();
    }

    public function merge()
    {
        return $this->php()->merge($this->json());
    }

    /**
     * Sync the translations with the originals
     *
     * @param string[] $paths
     */
    public function sync(array $paths)
    {
        $result = new TranslationCollection();

        /** @var string $path */
        foreach ($paths as $path) {
            if ($this->json()->where('path', $path)->isEmpty()) {
                $value = null;

                if ($this->php()->where('path', $path)->where('translation', '<>', '')->isNotEmpty()) {
                    $value = $this->php()->where('path', $path)->where('translation', '<>', '')->first()->translation;
                }

                $translation = new Translation($path, $value);
            } else {
                $translation = $this->json()->where('path', $path)->first();
            }

            $result->add($translation);
        }

        $this->save($result);
    }

    public function save(TranslationCollection $translations)
    {
        return (new JSONHandler($this->json_path))->save($translations);
    }
}