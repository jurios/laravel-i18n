<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Kodilab\LaravelFilters\Filterable;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Language extends Model
{
    use Filterable;

    protected $table;

    protected $fillable = ['enabled'];

    protected $casts = [
        'name' => 'string',
        'ISO_639_1' => 'string',
        'enabled' => 'boolean'
    ];

    protected $appends = [ 'perc' ];

    public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.tables.languages');
    }

    public static function __callStatic($method, $parameters)
    {
        if (in_array($method, self::all()->pluck('ISO_639_1')->toArray()))
        {
            return self::getLanguageFromISO_639_1($method);
        }

        return parent::__callStatic($method, $parameters);
    }

    //static methods
    /**
     * Returns the fallback language (it must exists)
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getFallbackLanguage()
    {
        if (session()->has('fallback_language'))
        {
            return session()->get('fallback_language');
        }

        $fallback_language = Language::with('translations')
            ->enabled()->where('ISO_639_1', config('app.fallback_locale'))->first();

        if(is_null($fallback_language))
        {
            throw new MissingLanguageException('Enabled fallback language (' . config('app.fallback_locale') . ') not found');
        }

        session()->flash('fallback_language', $fallback_language);

        return $fallback_language;
    }

    /**
     * Returns the user configured language. If it's not configured by middleware then fallback language is used
     * @return mixed
     * @throws MissingLanguageException
     */
    public static function getUserLanguage()
    {
        if (session()->has('user_language'))
        {
            return session()->get('user_language');
        }

        if (session()->has(config('i18n.session_var_name')))
        {
            $user_language = Language::where('ISO_639_1', session()->get(config('i18n.session_var_name')))->first();
            session()->flash('user_language', $user_language);
            return $user_language;
        }

        $user_language = self::getFallbackLanguage();
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

    public function getPercAttribute()
    {
        if ($this->isFallbackLanguage())
        {
            return 100;
        }

        $count_fallback_translations = count(self::getFallbackLanguage()->translations);

        if ($count_fallback_translations > 0)
        {
            return (int)number_format((count($this->translations) * 100) / $count_fallback_translations, 0);
        }

        return 0;
    }

    //Relationships
    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_id');
    }

    //Scopes
    public function scopeEnabled(Builder $query, bool  $value = true)
    {
        $query->where('enabled', $value);
    }

    //Methods
    public function isFallbackLanguage()
    {
        return $this->id === self::getFallbackLanguage()->id;
    }

    public function enable()
    {
        $this->enabled = true;
        $this->save();
    }

    public function disable()
    {
        if(!$this->isFallbackLanguage())
        {
            $this->enabled = false;
            $this->save();
        }
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
