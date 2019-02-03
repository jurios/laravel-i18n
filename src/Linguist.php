<?php

namespace Kodilab\LaravelI18n;


use Illuminate\Console\OutputStyle;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Models\Text;
use Kodilab\LaravelI18n\Models\Translation;

class Linguist
{
    protected $filesystem;

    protected $paths;

    protected $fallback_language;

    /** @var OutputStyle $output */
    protected $output;

    /**
     * Linguist constructor.
     *
     * @param Filesystem $filesystem
     * @param array $paths
     * @param OutputStyle|null $output
     */
    public function __construct(Filesystem $filesystem, array $paths, OutputStyle $output = null)
    {
        $this->filesystem = $filesystem;

        $this->paths = $paths;

        $this->fallback_language = config('app.fallback_locale');

        $this->output = $output;
    }

    public function setOutput(OutputStyle $output)
    {
        $this->output = $output;
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
    public function getAllTranslatableStringFromFiles()
    {
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
     * Transforms the array from getAllTranslatableStringFromFiles() to the next format:
     * [
     *      md5(text1) => [
     *          'text' => text1,
     *          'files' => [
     *              path/to/file1 => times_in_file1
     *              path/to/file2 => times_in_file2
     *          ]
     *      ],
     *      md5(text2) => text2,
     *      ....
     * ]
     *
     * @param array $translationsByFile
     * @return array
     */
    public function getTranslationsWithMd5(array $translationsByFile)
    {
        $result = [];

        foreach ($translationsByFile as $file => $translations)
        {
            foreach ($translations as $translation)
            {
                $md5 = md5($translation);

                if (!isset($result[$md5]))
                {
                    $result[$md5] = [
                        'text' => $translation,
                        'files' => [
                            $file => 1
                        ]
                    ];
                }
                else {
                    isset($result[$md5]['files'][$file])? $result[$md5]['files'][$file]++ : $result[$md5]['files'][$file] = 1;
                }
            }
        }

        return $result;
    }

    /**
     * Delete translation in database which is not present in $translations array
     *
     * @param array $translations
     * @return int
     */
    public function deleteDeprecatedTranslations(array $translations)
    {
        $md5 = array_keys($translations);

        $deprecated_query = Translation::whereNotIn('md5', $md5);

        /** @var Collection $deprecated */
        $deprecated = $deprecated_query->get();

        $deprecated_query->delete();

        Text::whereNotIn('md5', $md5)->get()->each(function ($item) {
            $item->delete();
        });

        return count($deprecated);
    }

    /**
     * Add the translations present in $translation which are not present in database
     *
     * @param array $translations
     * @return int
     */
    public function addNewTranslations(array $translations)
    {
        $count = 0;

        foreach ($translations as $md5 => $translation)
        {
            if (!Text::existsText($md5))
            {
                Text::create([
                    'md5' => $md5,
                    'text' => $translation['text'],
                    'paths' => $translation['files']
                ]);

                $count++;
            }
            else
            {

                Text::where('md5', $md5)->first()->update([
                    'paths' => $translation['files']
                ]);
            }
        }

        return $count;
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

    public function existsFallbackLocale()
    {
        try {
            Locale::getFallbackLocale();
            $exists = true;
        } catch (MissingLocaleException $exception) {
            $exists = false;
        }

        return $exists;
    }

    public function isAValidFallbackLocale(Locale $locale)
    {
        return $locale->ISO_639_1 === $this->fallback_language;
    }

    public function generateFallbackLocale()
    {
        // As we are generating a fallback locale, we should disable other fallback locales
        Locale::where('fallback', true)->update([
            'fallback' => false
        ]);

        $fallbackLocale = Locale::create([
            'ISO_639_1' => $this->fallback_language,
            'fallback' => true,
            'created_by_sync' => true,
            'enabled' => true
        ]);

        $this->sendOutput("\"<fg=green>Fallback locale created.</>\"");

        return $fallbackLocale;
    }

    private function sendOutput(string $message)
    {
        if (!is_null($this->output))
        {
            $this->output->writeln($message);
        }
    }
}