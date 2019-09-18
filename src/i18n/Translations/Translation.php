<?php


namespace Kodilab\LaravelI18n\i18n\Translations;


use Illuminate\Contracts\Support\Arrayable;

class Translation implements Arrayable
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var string
     */
    public $translation;

    public function __construct(string $path, string $translation = '')
    {
        $this->path = $path;
        $this->translation = $translation;
    }

    /**
     * Returns whether the translation is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->translation === '';
    }

    /**
     * Returns the dot path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Returns the translation
     *
     * @return string
     */
    public function getTranslation(): string
    {
        return $this->translation;
    }

    /**
     * Returns whether it is the same translation
     *
     * @param Translation $translation
     * @return bool
     */
    public function is(Translation $translation): bool
    {
        return $translation->getPath() === $this->getPath() &&
            $translation->getTranslation() === $this->getTranslation();
    }

    /**
     * Export to array
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'path' => $this->getPath(),
            'translation' => $this->getTranslation()
        ];
    }
}