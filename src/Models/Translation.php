<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Translation extends Model
{
    protected $table;

    protected $fillable = ['text', 'language_id', 'md5', 'text_id'];

    protected $casts = [
        'md5' => 'string',
        'text' => 'string',
        'language_id' => 'integer',
        'text_id' => 'integer'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.translation.table');
    }

    /**
     * Returns the translation of $text in $language language. If it doesn't exists, try to create it for the base language
     * @param $md5
     * @param $language
     * @return mixed|null
     * @throws MissingLanguageException
     */
    public static function getTranslation(string $text, Language $language)
    {

        $md5 = md5($text);

        /** @var Translation $translation */
        $translation = Translation::where('md5', $md5)->where('language_id', $language->id)->first();

        if(!is_null($translation))
        {
            return $translation->text;
        }

        self::generateTranslation($md5, $text);

        return null;
    }

    //relationships

    public function language()
    {
        return $this->belongsTo(Language::class, 'language_id');
    }

    public function text()
    {
        return $this->belongsTo(Text::class);
    }

    //scope methods

    /**
     * Returns whether the translation exists for the language $language
     * @param $md5
     * @param Language $language
     * @return bool
     */
    public static function existsTranslation($md5, Language $language)
    {
        return !is_null(Translation::where('md5', $md5)->where('language_id', $language->id)->first());
    }

    /**
     * Stores in database a new translation for the language $language and text $text. If $language is not base language,
     * the text persisted will be null and a base language translation
     * will be created with the text.
     * @param $md5
     * @param $text
     * @param Language|null $language
     * @throws MissingLanguageException
     */
    public static function generateTranslation($text, Language $language = null)
    {
        $md5 = md5($text);

        if (is_null($language) ||$language->id === Language::getBaseLanguage()->id)
        {
            self::generateBaseLanguageTranslation($md5, $text);
            return;
        }

        if (!self::existsTranslation($md5, $language))
        {
            Translation::create([
                'text' => null,
                'md5' => $md5,
                'language_id' => $language->id
            ]);
        }

        self::generateBaseLanguageTranslation($md5, $text);
    }

    /**
     * Stores in database a new translation for the base language.
     * @param $md5
     * @param $text
     * @throws MissingLanguageException
     */
    private static function generateBaseLanguageTranslation($md5, $text)
    {
        $base_language = Language::getBaseLanguage();

        if (!self::existsTranslation($md5, $base_language))
        {
            Translation::create([
                'text' => $text,
                'md5' => $md5,
                'language_id' => $base_language->id
            ]);
        }
    }
}
