<?php


namespace Kodilab\LaravelI18n\I18n;


use Illuminate\Support\Facades\Cache;
use Kodilab\LaravelI18n\Exceptions\MissingLocaleException;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Models\Translation;

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

        $translated_line = Translation::getTextTranslation($text, $locale, $honestly);

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
}