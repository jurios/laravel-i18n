<?php


namespace Kodilab\LaravelI18n\Traits;


use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;
use Kodilab\LaravelI18n\Models\Locale;

trait HasTranslations
{
    public function __get($name)
    {
        if ($this->isTranslatableAttribute($name)) {
            return $this->getTranslatedAttribute(app('i18n')->getLocale(), $name);
        }

        return parent::__get($name);
    }

    /**
     * @return BelongsToMany
     */
    public function translations()
    {
        return $this->belongsToMany(
            Locale::class, $this->getTranslationsTableName(), $this->getTranslatablePrimaryKey()
        )->withPivot($this->getTranslatableAttributes());
    }

    /**
     * Saves (or updates) one translated attribute
     *
     * @param Locale $locale
     * @param string $attribute
     * @param string $translation
     */
    public function setTranslatedAttribute(Locale $locale, string $attribute, string $translation)
    {
        $this->setTranslatedAttributes($locale, [$attribute => $translation]);
    }

    /**
     * Saves (or updates) multiple translated attributes
     *
     * @param Locale $locale
     * @param array $translation
     */
    public function setTranslatedAttributes(Locale $locale, array $translation)
    {
        $this->translations()->sync([$locale->id => $translation], false);
        $this->refresh();
    }

    public function isTranslated(Locale $locale, string $attribute)
    {
        if ($this->isTranslatableAttribute($attribute)) {

            if (!is_null($translation = $this->translations()->find($locale->id))) {
                return !is_null($translation->pivot->$attribute);
            }
        }

        return false;
    }

    /**
     * Returns the translated attribute for a given locale
     *
     * @param Locale $locale
     * @param string $field
     * @return |null
     */
    public function getTranslatedAttribute(Locale $locale, string $field)
    {
        if ($this->isTranslatableAttribute($field)) {
            return $this->translations()->find($locale->id)->pivot->$field;
        }

        return null;
    }

    /**
     * Returns the translation table name
     *
     * @return string
     */
    protected function getTranslationsTableName()
    {
        if (method_exists($this, 'getCustomTranslationTableName')) {
            return $this->getCustomTranslationTableName();
        }

        return $translation_table_name = Str::snake(class_basename($this) . 'Translations');
    }

    /**
     * Returns the foreign key used in the translations table
     *
     * @return string
     */
    protected function getTranslatablePrimaryKey()
    {
        if (method_exists($this, 'getCustomTranslatablePrimaryKey')) {
            return $this->getCustomTranslatablePrimaryKey();
        }

        return 'model_id';
    }

    /**
     * Returns all attributes casted to translatable
     *
     * @return array
     */
    private function getTranslatableAttributes()
    {
        return array_keys(array_filter($this->getCasts(), function ($attribute) {
            return $attribute === 'translatable';
        }));
    }

    /**
     * Returns whether an attribute is translatable
     *
     * @param string $attribute
     * @return bool
     */
    private function isTranslatableAttribute(string $attribute)
    {
        return in_array($attribute, $this->getTranslatableAttributes());
    }
}