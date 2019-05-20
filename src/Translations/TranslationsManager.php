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
        $this->translations = new Collection();
        $this->locale = $locale;
        $this->json_path = config('i18n.translations_path') . DIRECTORY_SEPARATOR . $locale->reference . '.json';
        $this->readTranslations();
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        }
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
     * Sync the translations with the original occurrences
     *
     * @param array $originals
     */
    public function sync(array $occurrences)
    {
        $translations = $this->addOccurrenceFlag();

        foreach ($occurrences as $occurrence) {

            if (!isset($translations[$occurrence])) {
                $translation = $this->locale->isFallback() ? $occurrence : "";
                $this->add($occurrence, $translation);
            }

            $translations[$occurrence]['occurrence'] = true;
        }

        foreach ($translations as $translation) {
            if ($translation['occurrence'] === false) {
                $this->delete($translation['original']);
            }
        }

    }

    /**
     * It returns the translation with a occurrence flag needed in order to detect deprecated translations during sync
     *
     */
    private function addOccurrenceFlag()
    {
       $translations = [];

        foreach ($this->translations as $original => $translation) {
            $translation['occurrence'] = false;
            $translations[$original] = $translation;
        }

        return $translations;
    }

    /**
     * Import the array format translations into the json translations and persist them into the file
     *
     * @param array $array
     */
    public function importArray(array $array)
    {
        $export = [];

        transformArrayTranslation(null, $array, $export);

        foreach ($export as $original => $translation) {
            $this->add($original, $translation);
        }
    }

    /**
     * Persists the current translation collection into the file
     */
    private function save()
    {
        $translation = $this->normalizeTranslationsArray();

        $json = json_encode($translation, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        file_put_contents($this->json_path, $json);
    }

    /**
     * Read the translation from the file and feed the translation collection
     */
    private function readTranslations()
    {
        if (file_exists($this->json_path)) {
            $translations = json_decode(file_get_contents($this->json_path), true);

            foreach ($translations as $original => $translation) {
                $this->addToTranslationsCollection($original, $translation);
            }
        }
    }

    private function addToTranslationsCollection(string $original, string $translation)
    {
        $this->translations->put($original, [
            'original' => $original,
            'translation' => $translation
        ]);
    }

    /**
     * This function returns the translation collection normalized in order to be persisted as json file
     */
    private function normalizeTranslationsArray()
    {
        //TODO: Look for a optimized way to do this

        $result = [];

        foreach ($this->translations as $item) {
            $original = $item['original'];
            $translation = $item['translation'];

            $result[$original] = $translation;
        }

        return $result;
    }
}