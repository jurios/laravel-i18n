<?php


namespace Kodilab\LaravelI18n\Translations\FileManager;


use Illuminate\Support\Collection;

class JSONManager extends FileManager implements FileManagerInterface
{

    /**
     * Get the translation from the file
     *
     * @return Collection
     */
    protected function getTranslationsFromFile()
    {
        $collection = new Collection();

        try {
            $content = json_decode(file_get_contents($this->path), true);
        } catch (\Exception $e) {
            $content = [];
        }

        foreach ($content as $original => $translation) {
            $collection->put($original, [
                'original' => $original,
                'translation' => $translation
            ]);
        }

        return $collection;
    }

    /**
     * Adds a new translation into the translation file
     *
     * @param string $original
     * @param string $translation
     */
    public function add(string $original, string $translation)
    {
        $this->addToCollection($original, $translation);

        $this->save();
    }

    /**
     * Updates a translation into the translation file
     *
     * @param string $original
     * @param string $translation
     */
    public function update(string $original, string $translation)
    {
        $this->removeFromCollection($original);
        $this->addToCollection($original, $translation);

        $this->save();
    }

    /**
     * Removes a translation fomr the translation file
     *
     * @param string $original
     */
    public function remove(string $original)
    {
        $this->removeFromCollection($original);

        $this->save();
    }


    /**
     * Remove the original item from the translation collection
     *
     * @param string $original
     */
    private function removeFromCollection(string $original)
    {
        $this->translations->forget($original);
    }

    /**
     * Add a new translation to the translation collection
     *
     * @param string $original
     * @param string $translation
     */
    private function addToCollection(string $original, string $translation)
    {
        $this->translations->put($original, [
            'original' => $original,
            'translation' => $translation
        ]);
    }

    /**
     * Save the translation collection into the json file
     */
    public function save()
    {
        $translations = $this->translations->map(function ($item) {
            return $item['translation'];
        });

        $json = json_encode($translations, JSON_PRETTY_PRINT);

        file_put_contents($this->path, $json);
    }
}