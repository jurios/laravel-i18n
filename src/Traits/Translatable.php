<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
            return $this->getTranslatedAttribute($name, Locale::getUserLocale()->language);
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
     * Get the translated $attribute_name using the $language
     *
     * @param string $attribute_name
     * @param Language $language
     * @param bool $honestly
     * @return mixed|string
     * @throws Exceptions\MissingLanguageException
     */
    public function getTranslatedAttribute(string $attribute_name, Language $language = null, $honestly = false)
    {
        $this->loadTranslations();

        if (is_null($language))
        {
            $language = Locale::getUserLocale()->language;
        }

        $translation = isset($this->translations[$language->id]) &&
            isset($this->translations[$language->id][$attribute_name]) ? $this->translations[$language->id][$attribute_name] : null;

        if (is_null($translation) && $language->id !== Language::getFallbackLanguage() && !$honestly)
        {
            return $this->getTranslatedAttribute($attribute_name, Language::getFallbackLanguage());
        }

        return !is_null($translation) ? $translation : "";
    }

    /**
     * Update the $attributes of a $language translation. The attributes which are not present in $attributes
     * will not be updated. If the translation doesn't exists then it will be created.
     *
     * @param array $attributes
     * @param null $language
     * @throws Exceptions\MissingLanguageException
     */
    public function updateTranslation(array $attributes, Language $language = null)
    {
        $this->loadTranslations();

        if (is_null($language))
        {
            $language = Language::getFallbackLanguage();
        }

        $attributes = $this->filterValidTranslatableAttributes($attributes);

        if (!$this->hasTranslation($language))
        {
            $this->createTranslation($attributes, $language);
            return;
        }

        $this->getTranslationQuery($language)->update($attributes);

        foreach ($attributes as $attribute => $value)
        {
            $this->translations[$language->id][$attribute] = $value;
        }
    }

    /**
     * Create a translation using the values in $attributes for a given $language
     * @param array $attributes
     * @param Language|null $language
     * @throws Exceptions\MissingLanguageException
     */
    public function createTranslation(array $attributes, Language $language = null)
    {
        $this->loadTranslations();

        if (is_null($language))
        {
            $language = Language::getFallbackLanguage();
        }

        if ($this->hasTranslation($language))
        {
            return;
        }

        $attributes = $this->filterValidTranslatableAttributes($attributes);

        // As $attributes could not contains all translatable attributes and we can't insert only a part of them
        // we need to fill the not present ones with as empty string
        $attributes = $this->fillTranslatedAttributes($attributes);

        $attributes['language_id'] = $language->id;
        $attributes['resource_id'] = $this->id;

        DB::table($this->getTranslationsTableName())->insert($attributes);

        unset($attributes['language_id']);
        unset($attributes['resource_id']);

        $this->translations[$language->id] = $attributes;
    }

    /**
     * Returns whether a translation for a language exists
     *
     * @param $language
     * @return bool
     */
    public function hasTranslation( Language $language)
    {
        $this->loadTranslations();

        return isset($this->translations[$language->id]);
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
                    $this->translations[$translation->language_id][$attribute] = $translation->$attribute;
                }
            }
        }
    }

    /**
     * Return the query of the translation
     * @param $language
     * @return \Illuminate\Database\Query\Builder
     */
    private function getTranslationQuery(Language $language = null)
    {
        $query = DB::table($this->getTranslationsTableName())->where('resource_id', $this->id);

        if (!is_null($language))
        {
            $query->where('language_id', $language->id);
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
                if ($name === 'language_id' || $name === 'resource_id' || $name === 'id') {
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