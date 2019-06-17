<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\Translations\Translation;

class Linguist
{
    protected $filesystem;

    protected $paths;

    /** @var OutputStyle $output */
    protected $output;

    /**
     * Linguist constructor.
     *
     * @param Filesystem $filesystem
     * @param array $paths
     * @param OutputStyle|null $output
     */
    public function __construct(Filesystem $filesystem, array $paths)
    {
        $this->filesystem = $filesystem;

        $this->paths = $paths;
    }

    /**
     * Returns every occurrence of translated text in the project. The array has the next format:
     * [
     *      'text' => 'translation (text)'
     * ]
     *
     * @return array
     */
    public function texts()
    {
        $result = [];

        foreach ($this->getAllTranslatableStringFromFiles() as $file => $occurrences) {
            foreach ($occurrences as $occurrence) {
                $result[$occurrence] = new Translation($occurrence, $occurrence);
            }
        }

        return $result;
    }

    /**
     * Look for translation call and extract the text in *.php files in $this->paths directories. It returns a array like this:
     *
     * [
     *      /path/to/file => [
     *          0 => text1,
     *          1 => text2
     *          * => text*
     *      ]
     * ]
     *
     * This functions is based on laravel-langman project by themsaid
     *
     * @return array
     */
    private function getAllTranslatableStringFromFiles()
    {
        /*
         * This pattern is derived from Barryvdh\TranslationManager by Barry vd. Heuvel <barryvdh@gmail.com>
         *
         * https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
         */

        $functions = ['t', '__'];

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $functions).')'.// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\'\"]".// Match " or '
            '('.// Start a new group to match:
            '[\s\S]*'.// Must start with group
            ')'.// Close group
            "[\'\"]".// Closing quote
            "[\),]"  // Close parentheses or new parameter
        ;

        $allMatches = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($this->filesystem->allFiles($this->paths) as $file) {

            if (preg_match_all("/$pattern/siU", $file->getContents(), $matches))
            {
                $allMatches[$file->getRelativePathname()] = $matches[2];
            }
        }

        return $allMatches;
    }

    /**
     * Look for dynamic translations ( uses t($var, ...) ) which can not be created statically
     *
     * @param array $translations
     * @return int
     */
    public function countDynamicTranslations()
    {

        $count = 0;

        /*
         * This pattern is derived from Barryvdh\TranslationManager by Barry vd. Heuvel <barryvdh@gmail.com>
         *
         * https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
         */

        $functions = ['t'];

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $functions).')'.// Must start with one of the functions
            "\(".// Match opening parentheses
            "[\$]".// Match $
            '('.// Start a new group to match:
            '[\s\S]*'.// Must start with group
            ')'.// Close group
            "[\),]"  // Close parentheses or new parameter
        ;

        $allMatches = [];

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($this->filesystem->allFiles($this->paths) as $file) {

            if (preg_match_all("/$pattern/siU", $file->getContents(), $matches))
            {
                $count++;
            }
        }

        return $count;
    }
}