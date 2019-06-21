<?php


namespace Kodilab\LaravelI18n\Translations;


use Illuminate\Contracts\Support\Arrayable;

class Translation implements Arrayable
{
    /** @var string */
    public $original;

    /** @var string */
    public $translation;

    public function __construct(string $original, string $translation = null)
    {
        $this->original = $original;
        $this->translation = $translation;
    }

    public function isEmpty()
    {
        return is_null($this->translation);
    }

    public function toArray()
    {
        return [
            'original' => $this->original,
            'translation' => $this->translation
        ];
    }
}