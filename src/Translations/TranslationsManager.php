<?php


namespace Kodilab\LaravelI18n\Translations;


use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Models\Locale;

class TranslationsManager
{
    /** @var Locale */
    protected $locale;

    /** @var string */
    protected $json_path;

    /** @var Collection */
    protected $translations;


    public function __construct(Locale $locale)
    {
        $this->locale = $locale;
        $this->json_path = config('i18n.translations_path', resource_path('lang')) . DIRECTORY_SEPARATOR . $locale->reference . '.json';
        $this->translations = $this->getTranslations($this->json_path);
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }

        if ($name === 'percentage') {
            return $this->generateTranslatedPercentage();
        }
    }

    /**
     * Returns all translations
     */
    public function all()
    {
        return $this->translations;
    }

    public function find(string $original)
    {
        return $this->translations->where('original', $original)->first();
    }

    /**
     * It will add the original's text translation into the translations collection. If the translations already exists,
     * it will be updated.
     *
     * @param string $original
     * @param string $translation
     */
    public function add(string $original, string $translation)
    {
        $this->addToTranslationsCollection($original, $translation);

        $this->save();
    }

    /**
     * Add alias
     * @param string $original
     * @param string $translation
     */
    public function update(string $original, string $translation)
    {
        $this->add($original, $translation);
    }

    public function merge(array $translations)
    {
        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $this->add($translation->original, $translation->translation);
        }
    }

    /**
     * Removes the 'original' translation from the collection and from the file
     *
     * @param string $original
     */
    public function delete(string $original)
    {
        $this->translations->forget($original);

        $this->save();
    }

    /**
     * Load the translations from the file
     */
    public function refresh()
    {
        $this->translations = $this->getTranslations($this->json_path);
    }

    /**
     * Persists the current translation collection into the file
     */
    private function save()
    {
        $translation = $this->normalizeTranslationsArray();

        if (count($translation) <= 0) {

            if (file_exists($this->json_path)) {
                unlink($this->json_path);
            }

            return;
        }

        $json = json_encode($translation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($this->json_path, $json);
    }

    /**
     * Returns the translation from the file.
     *
     * @param string $json_path
     * @return Collection
     */
    private function getTranslations(string $json_path)
    {
        $translations = new Collection();

        if (file_exists($this->json_path)) {
            $content = json_decode(file_get_contents($json_path), true);

            foreach ($content as $original => $translation) {
                $translations->put($original, new Translation($original, $translation));
            }
        }

        return $translations;
    }

    private function addToTranslationsCollection(string $original, string $translation)
    {
        $this->translations->put($original, new Translation($original, $translation));
    }

    /**
     * This function returns the translation collection normalized in order to be persisted as json file
     */
    private function normalizeTranslationsArray()
    {
        //TODO: Look for a optimized way to do this

        $result = [];

        /** @var Translation $item */
        foreach ($this->translations as $item) {
            $original = $item->original;
            $translation = $item->translation;

            $result[$original] = $translation;
        }

        return $result;
    }


    private function generateTranslatedPercentage()
    {
        $fallback_manager = new TranslationsManager(Locale::getFallbackLocale());

        $fallback_translations_count = count($fallback_manager->translations);

        return (int) round((count($this->translations) * 100) / $fallback_translations_count, 0);
    }
}