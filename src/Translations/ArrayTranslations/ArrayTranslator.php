<?php


namespace Kodilab\LaravelI18n\Translations\ArrayTranslations;


use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Translations\FileHandlers\ArrayFile;
use Kodilab\LaravelI18n\Translations\Translation;
use Symfony\Component\Finder\SplFileInfo;

class ArrayTranslator
{
    /**
     * Translation locale
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Locale translation directory (Could exists two paths. For a locale like "en_GB, could exists "en" directory and
     * "en_GB" directory
     *
     * @var string
     */
    protected $paths;

    /**
     * Filesystem instance
     *
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * Locale array translations from all files
     *
     * @var Collection
     */
    protected $translations;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->filesystem = new Filesystem();
        $this->paths = $this->directoriesLookUp($locale);

        $this->translations = $this->getTranslations($this->paths);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
    }

    /**
     * Finds a translation
     *
     * @param string $original
     * @return mixed
     */
    public function find(string $original)
    {
        return $this->translations->where('original', $original)->first();
    }

    /**
     * Returns the array translation paths from the locale. It looks for the simple locale definition and region-scoped
     * locale
     *
     * @param Locale $locale
     * @return array
     */
    private function directoriesLookUp(Locale $locale)
    {
        $paths = [];

        /*
         * If simple locale exists, it should be listed first because the region-scoped translations (second path if it exists)
         * must override the first translations in case of collisions. (foreach in getTranslations())
         *
         */
        if (is_dir($path = config('i18n.lang_path', resource_path('lang')) . DIRECTORY_SEPARATOR . $locale->iso)) {
            $paths[] = $path;
        }

        if (!is_null($locale->region)) {

            if (is_dir($path = config('i18n.lang_path', resource_path('lang')) . DIRECTORY_SEPARATOR . $locale->reference)) {
                $paths[] = $path;
            }

        }

        return $paths;
    }

    private function getTranslations(array $paths)
    {
        $translations = new Collection();


        foreach ($paths as $path) {
            $files = $this->filesystem->files($path);

            /** @var SplFileInfo $file */
            foreach ($files as $file) {
                $file_path = $file->getPathname();

                $handler = new ArrayFile($file_path);

                foreach ($handler->translations as $path => $translation) {
                    /*
                     * ArrayFile uses Arr::dot() method. This method, could returns empty arrays. That's the reason
                     * why where we check is string.
                    */
                    if (is_string($translation)) {
                        $translations->put($path, new Translation($path, $translation));
                    }
                }
            }

        }

        return $translations;
    }
}