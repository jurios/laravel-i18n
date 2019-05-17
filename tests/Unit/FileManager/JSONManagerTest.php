<?php


namespace Kodilab\LaravelI18n\Tests\Unit\FileManager;


use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Translations\FileManager\JSONManager;

class JSONManagerTest extends TestCase
{
    use WithFaker;

    /** @var string */
    protected $language;

    /** @var string */
    protected $directory;

    /** @var JSONManager */
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->createLangPath();

        $this->language = $this->faker->languageCode;

        $this->manager = new JSONManager($this->generatePath($this->language));
    }

    public function test_gettranslationsfromfile_returns_translation_array()
    {
        $this->addRandomTranslations($this->manager, 10);

        $this->assertEquals(Collection::class, get_class($this->manager->translations));

        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);

        $this->assertTrue($this->manager->translations->where('original', $original)->isNotEmpty());
    }

    public function test_gettranslationsfromfile_returns_empty_array_if_json_file_does_not_exist()
    {
        $this->assertEquals(Collection::class, get_class($this->manager->translations));
        $this->assertEquals(0, count($this->manager->translations));
    }

    public function test_add_translation_add_a_new_translation_to_the_collection()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->assertFalse($this->manager->translations->where('original', $original)->isNotEmpty());

        $this->manager->add($original, $translation);

        $this->assertTrue($this->manager->translations->where('original', $original)->isNotEmpty());
        $this->assertEquals($translation, $this->manager->translations->where('original', $original)->first()['translation']);
    }

    public function test_add_translation_save_the_new_translation_to_file()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);

        $path = $this->manager->path;

        $json_content = json_decode(file_get_contents($path), true);

        $this->assertTrue(array_key_exists($original, $json_content));
        $this->assertEquals($json_content[$original], $translation);
    }

    public function test_update_translation_updates_the_translation_collection()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;
        $translation2 = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);

        $this->assertEquals($translation, $this->manager->translations->where('original', $original)->first()['translation']);

        $this->manager->update($original, $translation2);

        $this->assertEquals($translation2, $this->manager->translations->where('original', $original)->first()['translation']);
    }

    public function test_update_translation_save_the_new_translation_to_file()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;
        $translation2 = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);
        $this->manager->update($original, $translation2);

        $path = $this->manager->path;

        $json_content = json_decode(file_get_contents($path), true);

        $this->assertTrue(array_key_exists($original, $json_content));
        $this->assertEquals($json_content[$original], $translation2);
    }

    public function test_remove_translation_remove_the_translation_collection()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);

        $this->assertTrue($this->manager->translations->where('original', $original)->isNotEmpty());

        $this->manager->remove($original);

        $this->assertFalse($this->manager->translations->where('original', $original)->isNotEmpty());
    }

    public function test_remove_translation_removes_the_translation_from_file()
    {
        $original = $this->faker->unique()->paragraph;
        $translation = $this->faker->unique()->paragraph;

        $this->manager->add($original, $translation);

        $path = $this->manager->path;

        $json_content = json_decode(file_get_contents($path), true);

        $this->assertTrue(array_key_exists($original, $json_content));

        $this->manager->remove($original, $translation);

        $json_content = json_decode(file_get_contents($path), true);

        $this->assertFalse(array_key_exists($original, $json_content));
    }


    /**
     * Generates the full path where the language JSON file should be present in test mode
     *
     * @param string $language
     * @return string
     */
    private function generatePath(string $language)
    {
        return $this->resource_path . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . $language . '.json';
    }

    private function addRandomTranslations(JSONManager $manager, int $count)
    {
        for ($i = 0; $i < $count; $i++) {
            $original = $this->faker->unique()->paragraph;
            $translation = $this->faker->unique()->paragraph;

            $manager->add($original, $translation);
        }
    }

    private function createLangPath()
    {
        if (!file_exists($this->resource_path . DIRECTORY_SEPARATOR . 'lang')) {
            mkdir($this->resource_path . DIRECTORY_SEPARATOR . 'lang', 0777, true);
        }
    }

}