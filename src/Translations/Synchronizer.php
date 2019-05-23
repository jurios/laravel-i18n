<?php


namespace Kodilab\LaravelI18n\Translations;


class Synchronizer
{
    /** @var TranslationsManager */
    protected $manager;

    /** @var array */
    protected $founds;

    /** @var array */
    protected $deprecated;

    /** @var array */
    protected $new;

    /** @var array */
    protected $exist;

    public function __construct(TranslationsManager $manager, array $founds)
    {
        $this->manager = $manager;
        $this->founds = $founds;

        $this->deprecated = [];
        $this->new = [];
        $this->exist = [];

        $this->fillTranslations();
        $this->sync();
    }

    private function fillTranslations()
    {
        /** @var Translation $translation */
        foreach ($this->manager->translations as $translation)
        {
            //We set all existing translations as deprecated, then will be compared with the originals found
            $this->deprecated[$translation->original] = true;

        }
    }

    private function sync()
    {
        foreach ($this->founds as $original) {
            if (isset($this->deprecated[$original])) {
                unset($this->deprecated[$original]);
                $this->exist[$original] = true;
            }

            if (!isset($this->deprecated[$original]) && !isset($this->exist[$original])) {
                $this->new[$original] = true;
            }
        }
    }

    public function new()
    {
        return array_keys($this->new);
    }

    public function deprecated()
    {
        return array_keys($this->deprecated);
    }

    public function exist()
    {
        return array_keys($this->exist);
    }
}