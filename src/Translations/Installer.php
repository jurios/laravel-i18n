<?php


namespace Kodilab\LaravelI18n\Translations;


use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Models\Locale;
use Symfony\Component\Finder\SplFileInfo;

class Installer
{
    /**
     * Laravel provides english translations array out of the box
     */
    const DEFAULT_REFERENCE = 'en';

    /** @var Locale */
    protected $locale;

    /** @var string */
    protected $array_reference;

    /** @var Filesystem */
    protected $filesystem;

    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->filesystem = new Filesystem();
        $this->reference = $locale->reference;

        if (!$this->existsArrayTranslations()) {
            $this->reference = self::DEFAULT_REFERENCE;
        }
    }

    public function install()
    {
        $translations = $this->getArrayTranslations();
        $manager = new TranslationsManager($this->locale);
        $manager->importArray($translations);
    }

    private function getArrayTranslations()
    {
        $translations = [];
        $files = $this->filesystem->files($this->getArrayTranslationsPath());

        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            $scope = $file->getFilename();
            $scope = str_replace('.php', '', $scope);
            $translations[$scope] = require_once $file->getPathname();
        }

        return $translations;

    }

    /**
     * Returns if the array translations path exists
     *
     * @return bool
     */
    private function existsArrayTranslations()
    {
        return is_dir($this->getArrayTranslationsPath());
    }

    /**
     * Generate the path for the given $this->reference language
     *
     * @return string
     */
    private function getArrayTranslationsPath()
    {
        return config('i18n.translations_path') . DIRECTORY_SEPARATOR . $this->reference;
    }
}