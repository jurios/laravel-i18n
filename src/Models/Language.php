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

    //static methods
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

        if (session()->has(config('i18n.session_var_name')))
        {
            $user_language = Language::where('ISO_639_1', session()->get(config('i18n.session_var_name')))->first();
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

    public function getPercAttribute()
    {
        if ($this->isBaseLanguage())
        {
            return 100;
        }

        $count_base_translations = count(self::getBaseLanguage()->translations);

        if ($count_base_translations > 0)
        {
            return (int)number_format((count($this->translations) * 100) / $count_base_translations, 0);
        }

        return 0;
    }

    //relationships
    public function translations()
    {
        return $this->hasMany(Translation::class, 'language_id');
    }

    //scopes
    public function scopeEnabled(Builder $query, bool  $value = true)
    {
        $query->where('enabled', $value);
    }

    // methods
    public function isBaseLanguage()
    {
        return $this->id === self::getBaseLanguage()->id;
    }

    public function enable()
    {
        $this->enabled = true;
        $this->save();
    }

    public function disable()
    {
        if(!$this->isBaseLanguage())
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
