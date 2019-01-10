<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait Translatable
{
    public function __get($name)
    {
        if ($this->isTranslatableAttribute($name))
        {
            return $this->getTranslatedAttribute($name, Language::getUserLanguage());
        }

        return parent::__get($name);
    }

    public function getTranslatedAttribute($attribute, $language =  null, $honestly = false)
    {
        if (is_null($language))
        {
            $language = Language::getUserLanguage();
        }

        $translation = DB::table($this->getTable() . '_i18n')->where('language_id', $language->id)
            ->where('resource_id', $this->id)->first([$attribute]);

        if (is_null($translation) && $language->id !== Language::getFallbackLanguage() && !$honestly)
        {
            return $this->getTranslatedAttribute($attribute, Language::getFallbackLanguage());
        }

        return !is_null($translation) ? $translation->$attribute : "";
    }

    private function isTranslatableAttribute(string $attribute)
    {
        $columns = $this->getTranslatableAttributes();

        foreach ($columns as $column)
        {
            if ($column === $attribute)
            {
                return true;
            }
        }

        return false;
    }

    private function getTranslatableAttributes()
    {
        $columns = Schema::getColumnListing($this->getTable() . '_i18n');

        foreach ($columns as $key => $name)
        {
            if ($name === 'language_id' || $name === 'resource_id' || $name === 'id')
            {
                unset($columns[$key]);
            }
        }

        return $columns;
    }
}