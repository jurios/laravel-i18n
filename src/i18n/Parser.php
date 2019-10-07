<?php

namespace Kodilab\LaravelI18n\i18n;


use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;

class Parser
{
    protected $filesystem;

    protected $paths;

    /** @var OutputStyle $output */
    protected $output;

    /**
     * Translation function names
     * @var array
     */
    protected $functions;

    /**
     * Linguist constructor.
     *
     * @param Filesystem $filesystem
     * @param array $paths
     * @param array $functions
     */
    public function __construct(array $paths, array $functions = ['__', 't'])
    {
        $this->filesystem = new Filesystem();

        $this->paths = $paths;

        $this->functions = $functions;
    }

    /**
     * Returns every occurrence of translated text in the project. The array has the next format:
     * [
     *      'text' => 'translation (text)'
     * ]
     *
     * @return TranslationCollection
     */
    public function texts()
    {
        $result = new TranslationCollection();

        foreach ($this->getTranslatableTexts() as $md5 => $text) {
                $result->add(new Translation($text, $text));
        }

        return $result;
    }

    /**
     * Returns the texts which uses the translation methods. In order to avoid duplicates, the index is de md5 of the text
     *
     * [
     *      [
     *          md5(text1) => text1,
     *          md5(text2) => text2
     *          ...
     *      ]
     * ]
     *
     * This functions is based on laravel-langman project by themsaid
     *
     * @return array
     */
    private function getTranslatableTexts()
    {
        /*
         * This pattern is derived from Barryvdh\TranslationManager by Barry vd. Heuvel <barryvdh@gmail.com>
         *
         * https://github.com/barryvdh/laravel-translation-manager/blob/master/src/Manager.php
         */

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $this->functions).')'.// Must start with one of the functions
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

            if (preg_match_all("/$pattern/siU", $file->getContents(), $matches)) {
                foreach ($matches[2] as $text) {
                    $allMatches[md5($text)] = $text;
                }
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

        $pattern =
            // See https://regex101.com/r/jS5fX0/4
            '[^\w]'. // Must not start with any alphanum or _
            '(?<!->)'. // Must not start with ->
            '('.implode('|', $this->functions).')'.// Must start with one of the functions
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