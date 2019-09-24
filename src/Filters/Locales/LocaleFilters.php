<?php


namespace Kodilab\LaravelI18n\Filters\Locales;


use Kodilab\LaravelFilters\Filters\QueryFilters;
use Kodilab\LaravelI18n\Models\Locale;

class LocaleFilters extends QueryFilters
{
    public function term(string $value = null)
    {
        if (is_null($value)) {
            return;
        }

        $this->query->where('language', 'like', '%' . $value . '%');
    }

    public function translations(string $value = null)
    {
        if (is_null($value)) {
            return;
        }

        $cloned_query = clone $this->query;

        $locales = $cloned_query->get();

        $data = [];

        /** @var Locale $locale */
        foreach ($locales as $locale) {
            $data[$locale->id] = $locale->percentage;
        }

        foreach ($data as $locale_id => $percentage) {
            if ($value === 'translated' && $percentage < 100) {
                unset($data[$locale_id]);
            }

            if ($value === 'untranslated' && $percentage >= 100) {
                unset($data[$locale_id]);
            }
        }

        $locale_ids = array_keys($data);

        $this->query->whereIn('id', $locale_ids);
    }
}