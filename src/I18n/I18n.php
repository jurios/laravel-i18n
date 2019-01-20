<?php


namespace Kodilab\LaravelI18n;


use Illuminate\Support\Facades\Cache;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;

class I18n
{
    public function __construct()
    {
    }

    /**
     * Get the stored translation for the text $text using the variable replacement in $replace for the locale $locale.
     *
     * @param string $text
     * @param array $replace
     * @param Locale|null $locale
     * @param bool $honestly
     * @return string
     * @throws MissingLocaleException
     */
    public function translate(string $text, $replace = [], Locale $locale = null, bool $honestly = false)
    {
        /** @var Locale $locale */
        $locale = is_null($locale) ? Locale::getUserLocale() : $locale;

        if (is_null($locale))
        {
            throw new MissingLocaleException('Locale not found');
        }

        $translated_line = $this->getTranslation($text, $locale, $honestly);

        return $this->makeReplacements($translated_line, $replace);
    }

    /**
     * Make the place-holder replacements on a line. (Based on Laravel official translation system)
     *
     * @param  string  $line
     * @param  array   $replace
     * @return string
     */
    private function makeReplacements($line, array $replace)
    {
        if (empty($replace)) {
            return $line;
        }
        $replace = $this->sortReplacements($replace);
        foreach ($replace as $key => $value) {
            $line = str_replace(
                [':'.$key, ':'.Str::upper($key), ':'.Str::ucfirst($key)],
                [$value, Str::upper($value), Str::ucfirst($value)],
                $line
            );
        }
        return $line;
    }

    /**
     * Sort the replacements array. (Based on Laravel official translation system)
     *
     * @param  array  $replace
     * @return array
     */
    protected function sortReplacements(array $replace)
    {
        return (new Collection($replace))->sortBy(function ($value, $key) {
            return mb_strlen($key) * -1;
        })->all();
    }

    /**
     * Return the translation text for $text using $locale locale. It will use the fallback locale
     * if a translation has not been found in the user locale.
     *
     * @param $text
     * @param $locale
     * @return mixed
     * @throws MissingLocaleException
     */
    private function getTranslation($text, Locale $locale, $honestly)
    {
        $md5 = md5($text);

        if ($honestly)
        {
            return $this->getTranslationTextFromDataBase($text, $locale);
        }

        if (Cache::has($locale->reference . '_' . $md5 ))
        {
            return Cache::get($locale->reference . '_' . $md5);
        }

        $translated_line = $this->getTranslationTextFromDataBase($text, $locale);

        if (is_null($translated_line))
        {
            $translated_line = $text;

            if(!$locale->isFallbackLocale())
            {
                $translated_line = $this->getTranslation($text, Locale::getFallbackLocale(), $honestly);
            }
        }

        Cache::add($locale->reference . '_' . $md5, $translated_line, 60);

        return $translated_line;
    }

    /**
     * Retrieve the translation from the database if it exists.
     *
     * @param $text
     * @param $locale
     * @return mixed|null
     */
    private function getTranslationTextFromDataBase($text, Locale $locale)
    {
        $translation = Translation::getTranslationByText($text, $locale);

        if (!is_null($translation))
        {
            return $translation->translation;
        }

        return null;
    }

}