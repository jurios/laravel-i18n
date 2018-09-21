<?php


namespace Kodilab\LaravelI18n;


use Illuminate\Support\Facades\Cache;
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
    public function translate(string $text, $replace = [], $locale = null, $honestly = false)
    {
        $language = is_null($locale) ? Language::getUserLanguage() : Language::getLanguageFromISO_639_1($locale);

        if (is_null($language))
        {
            throw new MissingLanguageException('Language with reference' . $locale . 'not found when translating');
        }

        $translated_line = $this->getTranslation($text, $language);

        if (is_null($translated_line) && $honestly === false)
        {
            $translated_line = $this->getTranslation($text, Language::getBaseLanguage());
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
    private function getTranslation($text, Language $language)
    {
        $md5 = md5($text);

        if (Cache::has($language->reference . '_' . $md5 ))
        {
            return Cache::get($language->reference . '_' . $md5);
        }
        else
        {
            $translated_line = $this->getTranslationTextFromDataBase($text, $language);
            Cache::add($language->reference . '_' . $md5, $translated_line, 60);
        }
    }

    /**
     * Retrieve the translation from the database if it exists.
     *
     * @param $text
     * @param $locale
     * @return mixed|null
     * @throws MissingLanguageException
     */
    private function getTranslationTextFromDataBase($text, Language $language)
    {
        $translation = Translation::getTranslationByText($text, $language);

        if (!is_null($translation))
        {
            return $translation->translation;
        }

        return null;
    }

}