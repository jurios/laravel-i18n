<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Kodilab\LaravelFilters\Filterable;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Language extends Model
{
    use Filterable;

    protected $table;

    protected $fillable = ['default'];

    protected $casts = [
        'name' => 'string',
        'ISO_639_1' => 'string',
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
        if (session()->has('default_language'))
        {
            return session()->get('default_language');
        }

        $default_language = Language::where('default', true)->first();

        if(is_null($default_language))
        {
            throw new MissingLanguageException('Defaut Language not found');
        }

        session()->flash('default_language', $default_language);

        return $default_language;
    }

    /**
     * Returns the base language (it must exists)
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getBaseLanguage()
    {
        if (session()->has('base_language'))
        {
            return session()->get('base_language');
        }

        $base_language = Language::with('translations')->where('ISO_639_1', config('app.locale'))->first();

        if(is_null($base_language))
        {
            throw new MissingLanguageException('Base language (' . config('app.locale') . ') not found');
        }

        session()->flash('base_language', $base_language);

        return $base_language;
    }

    /**
     * Returns the user configured language. If it's not configured by middleware then base language is used
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getUserLanguage()
    {
        if (session()->has('user_language'))
        {
            return session()->get('user_language');
        }

        if (session()->has('locale'))
        {
            $user_language = Language::where('ISO_639_1', session()->get('locale'))->first();
            session()->flash('user_language', $user_language);
            return $user_language;
        }

        $user_language = self::getBaseLanguage();
        session()->flash('user_language', $user_language);
        return $user_language;
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

    public function isDefaultLanguage()
    {
        return $this->id === self::getDefaultLanguage()->id;
    }

    public function isBaseLanguage()
    {
        return $this->id === self::getBaseLanguage()->id;
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
