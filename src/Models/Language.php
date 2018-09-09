<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Language extends Model
{
    protected $table;

    protected $fillable = ['default', 'enabled'];

    protected $casts = [
        'name' => 'string',
        'ISO_639_1' => 'string',
        'enabled' => 'boolean',
        'default' => 'boolean'
    ];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.language.table');
    }

    //static methods
    public static function getDefaultLanguage()
    {
        $default_language = Language::where('default', true)->first();

        if(is_null($default_language))
        {
            throw new MissingLanguageException('Defaut Language not found');
        }

        return $default_language;
    }

    public static function getBaseLanguage()
    {
        $base_language = Language::where('ISO_639_1', config('app.locale'))->first();

        if(is_null($base_language))
        {
            throw new MissingLanguageException('Base language (' . config('app.locale') . ') not found');
        }

        return $base_language;
    }

    public static function getLanguageFromISO_639_1($iso)
    {
        return Language::where('ISO_639_1', $iso)->first();
    }

    public static function boot()
    {
        parent::boot();

        self::saving(function(Language $model){

            if ($model->default === true)
            {
                $model->enabled = true;
            }

            //TODO: We should ensure if the model was default and it's not now, then another language is default.
            //TODO: We should ensure if the model is the default, then there isn't another default language.

            return $model;
        });
    }

    //Accessors
    public function getReferenceAttribute()
    {
        return $this->ISO_639_1;
    }

    //relationships
    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_id');
    }

    //scope methods
    public function scopeEnabled(Builder $query, $value = true)
    {
        return $query->where('enabled', $value);
    }
}
