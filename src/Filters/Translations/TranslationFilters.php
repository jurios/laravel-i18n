<?php


namespace Kodilab\LaravelI18n\Filters\Translations;


use Illuminate\Support\Str;
use Kodilab\LaravelFilters\Filters\CollectionFilters;
use Kodilab\LaravelI18n\Translations\Translation;

class TranslationFilters extends CollectionFilters
{
    public function term($value = null)
    {
        if (is_null($value)) {
            return $this->results;
        }

        return $this->results->filter(function (Translation $translation, string $key) use ($value) {
            return preg_match('/'. Str::slug($value, "") . '/', Str::slug($translation->original, ""))
                || preg_match('/'. Str::slug($value, "") . '/', Str::slug($translation->translation, ""));
        });
    }

    public function status($value = null)
    {
        if (is_null($value)) {
            return $this->results;
        }

        if ($value !== 'translated' && $value !== 'untranslated') {
            return $this->results;
        }

        return $this->results->filter(function (Translation $translation, string $key) use ($value) {
            return $value === 'translated' ? !is_null($translation->translation) : is_null($translation->translation);
        });
    }
}