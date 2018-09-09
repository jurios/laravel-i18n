<?php


namespace Kodilab\LaravelI18n;


use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class I18n
{
    public function __construct()
    {
    }

    /**
     * Get the stored translation for the text $text using the variable replacement in $replace for the language
     * reference $locale. If $honestly is false, when translation doesn't exists, it will try to get the default language
     * translations. If that's not defined too, then base translation is returned.
     *
     * @param string $text
     * @param array $replace
     * @param null $locale
     * @param bool $honestly
     * @return string
     * @throws MissingLanguageException
     */
    public function getTranslationFromText(string $text, $replace = [], $locale = null, $honestly = false)
    {
        if (is_null($locale))
        {
            $locale = $this->getSessionLocale();
        }

        $translated_line = $this->getTranslatedLine($text, $locale);

        if (is_null($translated_line) && $honestly === false)
        {
            $translated_line = $this->getTranslatedLine($text, Language::getDefaultLanguage()->reference);

            if (is_null($translated_line))
            {
                $translated_line = $this->getTranslatedLine($text, Language::getBaseLanguage()->reference);
            }
        }

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
     * Return the translation text for $text in $locale language
     *
     * @param $text
     * @param $locale
     * @return mixed
     * @throws MissingLanguageException
     */
    private function getTranslatedLine($text, $locale)
    {
        $md5 = md5($text);
        if (Cache::has($locale . '_' . $md5 ))
        {
            return Cache::get($locale . '_' . $md5);
        }
        else
        {
            $translated_line = $this->getTranslationFromDataBase($text, $locale);
            Cache::add($locale . '_' . $md5, $translated_line, 60);
        }
    }

    /**
     * Returns the locale for the session. If it's not present, returns de default language (which is the language in use)
     * @return mixed
     * @throws MissingLanguageException
     */
    private function getSessionLocale()
    {
        if(Session::has('locale'))
        {
            return Session::get('locale');
        }

        return Language::getDefaultLanguage()->reference;
    }

    /**
     * Retrieve the translation from the database if it exists.
     *
     * @param $text
     * @param $locale
     * @return mixed|null
     * @throws MissingLanguageException
     */
    private function getTranslationFromDataBase($text, $locale)
    {
        $language = Language::getLanguageFromISO_639_1($locale);

        if (is_null($language))
        {
            throw new MissingLanguageException(
                'Language with ISO_639_1 = ' . $locale . ' not found in ' . config('i18n.language.table') . ' table'
            );
        }

        $language = Language::getLanguageFromISO_639_1($locale);

        return Translation::getTranslation($text, $language);
    }

}