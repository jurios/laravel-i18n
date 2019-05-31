<?php


namespace Kodilab\LaravelI18n\Translations;


class Synchronizer
{
    /** @var TranslationsManager */
    protected $manager;

    /** @var array */
    protected $translations;

    /** @var array */
    protected $deprecated;

    /** @var array */
    protected $new;

    /** @var array */
    protected $exist;

    public function __construct(TranslationsManager $manager, array $translations)
    {
        $this->manager = $manager;
        $this->translations = $translations;

        $this->deprecated = [];
        $this->new = [];
        $this->exist = [];

        $this->sync();
    }

    /**
     * Set all translations as deprecated
     */
    private function setAllDeprecated()
    {
        /** @var Translation $translation */
        foreach ($this->manager->translations as $translation)
        {
            $this->deprecated[$translation->original] = $translation;
        }
    }

    private function sync()
    {
        //We set all existing translations as deprecated, then will be compared with the originals found
        $this->setAllDeprecated();

        /** @var Translation $translation */
        foreach ($this->translations as $translation) {

            if (isset($this->deprecated[$translation->original])) {
                unset($this->deprecated[$translation->original]);
                $this->exist[$translation->original] = $translation;
            }

            if (!isset($this->deprecated[$translation->original]) && !isset($this->exist[$translation->original])) {
                $this->new[$translation->original] = $translation;
            }
        }
    }

    public function new()
    {
        return $this->new;
    }

    public function deprecated()
    {
        return $this->deprecated;
    }

    public function exist()
    {
        return $this->exist;
    }
}