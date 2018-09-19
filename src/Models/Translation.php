<?php

namespace Kodilab\LaravelI18n;

use Illuminate\Database\Eloquent\Model;
use Kodilab\LaravelFilters\Filterable;
use Kodilab\LaravelI18n\Exceptions\MissingLanguageException;

class Translation extends Model
{

    use Filterable;

    protected $table;

    protected $fillable = ['text', 'language_id', 'md5', 'text_id', 'needs_revision'];

    protected $casts = [
        'md5' => 'string',
        'text' => 'string',
        'language_id' => 'integer',
        'text_id' => 'integer',
        'needs_revision' => 'boolean'
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('i18n.translation.table');
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

    // methods
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
        $base_language = Language::getBaseLanguage();
        $md5 = md5($text);
        $translated_text = $language->id === $base_language->id ? $text : null; // $text contains the base language text
                                                                                // thus we only use it if the language
                                                                                // is the base language
        if (is_null($language))
        {
            $language = $base_language;
        }

        if (!self::existsTranslation($md5, $language))
        {
            $translation = Translation::create([
                'text' => $translated_text,
                'md5' => $md5,
                'language_id' => $language->id
            ]);
        } else {
            $translation = $language->getTranslation($md5);
        }

        if ($base_language->id !== $language->id)
        {
            // Create base language translation
            self::generateTranslation($text, $base_language);
        }

        return $translation;
    }

    /**
     * Returns the translation of $text in $language language. If it doesn't exists, try to create it for the base language
     * @param $md5
     * @param $language
     * @return mixed|null
     * @throws MissingLanguageException
     */
    public static function getTranslationByText(string $text, Language $language)
    {
        $md5 = md5($text);

        $translation = self::getTranslationByMd5($md5, $language);

        if(!is_null($translation))
        {
            return $translation;
        }

        return self::generateTranslation($text, $language);
    }

    public static function getTranslationByMd5(string $md5, Language $language)
    {

        /** @var Translation $translation */
        $translation = Translation::where('md5', $md5)->where('language_id', $language->id)->first();

        if(!is_null($translation))
        {
            return $translation;
        }

        return null;
    }

    public static function getLanguageTranslations(Language $language)
    {
        return Translation::where('language_id', $language)->get();
    }
}
