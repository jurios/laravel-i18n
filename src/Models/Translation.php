<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Translation extends Model
{
    protected $table;

    protected $fillable = ['text', 'language_id', 'md5'];

    protected $casts = [
        'md5' => 'string',
        'text' => 'string',
        'language_id' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.translation.table');
    }

    /**
     * @param $md5
     * @param $language
     * @return mixed|null
     * @throws MissingLanguageException
     */
    public static function getTranslation($text, $language)
    {
        if (!$language instanceof Language)
        {
            $language = Language::getLanguageFromISO_639_1($language);

            if (is_null($language))
            {
                throw new MissingLanguageException("Language reference " . $language . "not found");
            }
        }

        $md5 = md5($text);

        /** @var Translation $translation */
        $translation = Translation::where('md5', $md5)->where('language_id', $language->id)->first();

        if(!is_null($translation))
        {
            return $translation->text;
        }

        self::generateBlankTranslation($md5, $text, $language);

        return null;
    }

    //relationships

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    //scope methods

    private static function generateBlankTranslation($md5, $text, Language $language)
    {
        /** @var Language $base_language */
        $base_language = Language::getBaseLanguage();

        if ($base_language->id === $language->id )
        {
            self::generateBaseLanguageTranslation($md5, $text);
            return;
        }

        Translation::create([
            'text' => null,
            'md5' => $md5,
            'language_id' => $language->id
        ]);

        self::generateBaseLanguageTranslation($md5, $text);
    }

    private static function generateBaseLanguageTranslation($md5, $text)
    {
        Translation::create([
            'text' => $text,
            'md5' => $md5,
            'language_id' => Language::getBaseLanguage()->id
        ]);
    }
}
