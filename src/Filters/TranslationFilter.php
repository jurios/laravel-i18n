<?php

namespace Kodilab\LaravelI18n\Filters;


use Kodilab\LaravelFilters\QueryFilter;
use Kodilab\LaravelI18n\Models\Locale;

class TranslationFilter extends QueryFilter
{
    protected $translated_locale;

    public function setTranslatedLocale(Locale $locale)
    {
        $this->translated_locale = $locale;
    }

    public function translation($value = null)
    {
        if(is_null($value))
        {
            return $this->query;
        }

        $translated_locale_translations_md5 = $this->translated_locale->translations()
            ->where('translation', 'like', '%' . $value . '%')->get()->pluck('md5')->toArray();

        $query = clone $this->query;

        $fallback_locale_translations_md5 = $query->where('translation', 'like', '%' . $value . '%')->get()
            ->pluck('md5')->toArray();

        $md5s = array_unique(array_merge($translated_locale_translations_md5, $fallback_locale_translations_md5), SORT_REGULAR);

        return $this->query->whereIn('md5', $md5s);
    }

    public function needs_revision($value = null)
    {
        $query = clone $this->query;

        $md5s = $query->get()->pluck('md5')->toArray();

        $translations_md5s = $this->translated_locale->translations()->whereIn('md5', $md5s)
            ->where('needs_revision', true)->get()->pluck('md5')->toArray();

        return $this->query->whereIn('md5', $translations_md5s);
    }

    public function status($value)
    {
        $query = clone $this->query;

        $md5s = $query->get()->pluck('md5')->toArray();

        if ($value === 'translated')
        {
            $translations_md5s = $this->translated_locale->translations()->whereIn('md5', $md5s)
                ->get()->pluck('md5')->toArray();

            return $this->query->whereIn('md5', $translations_md5s);
        }

        if ($value === 'untranslated')
        {
            $translations_md5s = $this->translated_locale->translations()->whereIn('md5', $md5s)
                ->get()->pluck('md5')->toArray();

            return $this->query->whereNotIn('md5', $translations_md5s);
        }

        return $this->query;
    }


}