<?php

namespace Kodilab\LaravelI18n\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kodilab\LaravelI18n\Models\Locale;

trait Translatable
{
    /**
     * Loaded translatable column names in memory
     * @var null
     */
    protected $available_translatable_attributes = null;

    /**
     * Loaded translations in memory
     *
     * @var null
     */
    protected $translations = null;

    public function __get($name)
    {
        if ($this->isTranslatableAttribute($name))
        {
            return $this->getTranslatedAttribute($name, Locale::getUserLocale());
        }

        return parent::__get($name);
    }

    public function __call($name, $arguments)
    {
        if ($this->isTranslatableAttribute($name))
        {
            return $this->getTranslatedAttribute($name, ...$arguments);
        }

        return parent::__call($name, $arguments);
    }

    /**
     * Scope for order a resource by a translatable field
     *
     * @param Builder $query
     * @param string $attribute
     * @param Locale|null $locale
     * @param string $direction
     * @return Builder
     * @throws \Kodilab\LaravelI18n\Exceptions\MissingLocaleException
     */
    public function scopeOrderByTranslatedAttribute(Builder $query, string $attribute,
        string $direction = 'asc', Locale $locale = null)
    {
        if (is_null($locale))
        {
            $locale = Locale::getUserLocale();
        }

        $local_query = clone $query;

        $resource_ids = $local_query->get()->pluck('id')->toArray();

        $ordered_translations = DB::table($this->getTranslationsTableName())
            ->whereIn('resource_id', $resource_ids)
            ->where('locale_id', $locale->id)
            ->orderBy($attribute, $direction)->get();

        //TODO: Deal with resources without translation

        $resource_ids = $ordered_translations->pluck('resource_id')->toArray();

        if(count($resource_ids) > 0) {
            return $query->orderByRaw("field(id," . implode(',', $resource_ids) . ")");
        }

        return $query->where('id', -1);
    }

    /**
     * Get the translated $attribute_name using the $locale
     *
     * @param string $attribute_name
     * @param Locale|null $locale
     * @param bool $honestly
     * @return null|string
     * @throws Exceptions\MissingLocaleException
     */
    public function getTranslatedAttribute(string $attribute_name, Locale $locale = null, $honestly = false)
    {
        $this->loadTranslations();

        if (is_null($locale))
        {
            $locale = Locale::getUserLocale();
        }

        $translation = isset($this->translations[$locale->id]) &&
            isset($this->translations[$locale->id][$attribute_name]) ? $this->translations[$locale->id][$attribute_name] : null;

        if (is_null($translation) && $locale->id !== Locale::getFallbackLocale()->id && !$honestly)
        {
            return $this->getTranslatedAttribute($attribute_name, Locale::getFallbackLocale());
        }

        return !is_null($translation) ? $translation : "";
    }

    /**
     * Update the $attributes of a $locale translation. The attributes which are not present in $attributes
     * will not be updated. If the translation doesn't exists then it will be created.
     *
     * @param array $attributes
     * @param Locale|null $locale
     * @throws Exceptions\MissingLocaleException
     */
    public function updateTranslation(array $attributes, Locale $locale = null)
    {
        $this->loadTranslations();

        if (is_null($locale))
        {
            $locale = Locale::getFallbackLocale();
        }

        $attributes = $this->filterValidTranslatableAttributes($attributes);

        if (!$this->hasTranslation($locale))
        {
            $this->createTranslation($attributes, $locale);
            return;
        }

        $this->getTranslationQuery($locale)->update($attributes);

        foreach ($attributes as $attribute => $value)
        {
            $this->translations[$locale->id][$attribute] = $value;
        }
    }

    /**
     * Create a translation using the values in $attributes for a given $locale
     *
     * @param array $attributes
     * @param Locale|null $locale
     * @throws Exceptions\MissingLocaleException
     */
    public function createTranslation(array $attributes, Locale $locale = null)
    {
        $this->loadTranslations();

        if (is_null($locale))
        {
            $locale = Locale::getFallbackLocale();
        }

        if ($this->hasTranslation($locale))
        {
            return;
        }

        $attributes = $this->filterValidTranslatableAttributes($attributes);

        // As $attributes could not contains all translatable attributes and we can't insert only a part of them
        // we need to fill the not present ones with as empty string
        $attributes = $this->fillTranslatedAttributes($attributes);

        $attributes['locale_id'] = $locale->id;
        $attributes['resource_id'] = $this->id;

        DB::table($this->getTranslationsTableName())->insert($attributes);

        unset($attributes['locale_id']);
        unset($attributes['resource_id']);

        $this->translations[$locale->id] = $attributes;
    }

    /**
     * Returns whether a translation for a locale exists
     *
     * @param Locale $locale
     * @return bool
     */
    public function hasTranslation(Locale $locale)
    {
        $this->loadTranslations();

        return isset($this->translations[$locale->id]);
    }

    /**
     * Load translation from database and keep them in memory
     */
    private function loadTranslations()
    {
        if (is_null($this->translations))
        {
            $stored_translations = $this->getTranslationQuery()->get();

            foreach ($stored_translations as $translation) {
                $available_attributes = $this->getTranslatableAttributes();

                foreach ($available_attributes as $attribute)
                {
                    $this->translations[$translation->locale_id][$attribute] = $translation->$attribute;
                }
            }
        }
    }

    /**
     * Return the query of the translation
     * @param Locale $locale
     * @return \Illuminate\Database\Query\Builder
     */
    private function getTranslationQuery(Locale $locale = null)
    {
        $query = DB::table($this->getTranslationsTableName())->where('resource_id', $this->id);

        if (!is_null($locale))
        {
            $query->where('locale_id', $locale->id);
        }

        return $query;
    }

    /**
     * Returns whether $attribute is a translatable attribute (has a column in the translation table)
     *
     * @param string $attribute
     * @return bool
     */
    private function isTranslatableAttribute(string $attribute_name)
    {
        $columns = $this->getTranslatableAttributes();

        foreach ($columns as $column)
        {
            if ($column === $attribute_name)
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Remove attributes from $attributes which are not translatable
     *
     * @param array $attributes
     * @return array
     */
    private function filterValidTranslatableAttributes(array $attributes)
    {
        $expected = $this->getTranslatableAttributes();

        foreach ($attributes as $name => $value)
        {
            if (!in_array($name, $expected))
            {
                unset($attributes[$name]);
            }
        }

        return $attributes;
    }

    /**
     * Fill with "" the translated attributes which are not present in $attributes
     *
     * @param array $attributes
     * @return array
     */
    private function fillTranslatedAttributes(array $attributes)
    {
        $expecteds = $this->getTranslatableAttributes();

        $names = array_keys($attributes);

        foreach ($expecteds as $expected)
        {
            if (!in_array($expected, $names))
            {
                $attributes[$expected] = "";
            }
        }

        return $attributes;
    }

    /**
     * Get the columns present in the translation table which are attributes
     * @return mixed
     */
    private function getTranslatableAttributes()
    {
        if (is_null($this->available_translatable_attributes))
        {
            $columns = Schema::getColumnListing($this->getTranslationsTableName());

            foreach ($columns as $key => $name) {
                if ($name === 'locale_id' || $name === 'resource_id' || $name === 'id') {
                    unset($columns[$key]);
                }
            }

            $this->available_translatable_attributes = $columns;
        }

        return $this->available_translatable_attributes;
    }

    /**
     * Get the name of the translations table
     * @return string
     */
    private function getTranslationsTableName()
    {
        return $this->getTable() . config('i18n.tables.model_translations_suffix', '_i18n');
    }
}