<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
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

    /**
     * Returns the default language (it must exists)
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getDefaultLanguage()
    {
        $default_language = Language::where('default', true)->first();

        if(is_null($default_language))
        {
            throw new MissingLanguageException('Defaut Language not found');
        }

        return $default_language;
    }

    /**
     * Returns the base language (it must exists)
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getBaseLanguage()
    {
        if (Cache::has('base_language'))
        {
            return Cache::get('base_language');
        }

        $base_language = Language::where('ISO_639_1', config('app.locale'))->first();

        if(is_null($base_language))
        {
            throw new MissingLanguageException('Base language (' . config('app.locale') . ') not found');
        }

        Cache::forever('base_language', $base_language);

        return $base_language;
    }

    /**
     * Returns the user configured language. If it's not configured by middleware then base language is used
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getUserLanguage()
    {
        if (session()->has('locale'))
        {
            return Language::where('ISO_639_1', session()->get('locale'))->first();
        }

        return self::getBaseLanguage();
    }

    /**
     * Returns the language whose ISO_639_1 is $iso
     * @param $iso
     * @return mixed
     */
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

    /**
     * @param string $md5
     * @param string $text
     * @throws \Exception
     */
    public function setTranslation(string $md5, string $text, bool $needs_revision = false)
    {
        $text = Text::where('md5', $md5)->first();
        $translation = $this->translations()->where('md5', $md5)->first();

        if (is_null($text))
        {
            //TODO: Create a custom excepction
            throw new \Exception('No text found for md5: ' . $md5);
        }

        if(is_null($translation))
        {
            $translation = Translation::create([
                'text' => $text,
                'language_id' => $this->id,
                'md5' => $md5,
                'text_id' => $text->id,
                'needs_revision' => $needs_revision
            ]);
        }
        else {
            $translation->update([
                'text' => $text,
                'needs_revision' => $needs_revision
            ]);
        }

        return $translation;
    }

    public function getTranslation(string $md5)
    {
        return $this->translations()->where('md5', $md5)->first();
    }
}
