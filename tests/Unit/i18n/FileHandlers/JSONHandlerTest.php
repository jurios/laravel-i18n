<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n\FileHandlers;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\i18n\FileHandlers\PHPHandler;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Tests\TestCase;

class JSONHandlerTest extends TestCase
{
    use WithFaker;
    /**
     * @var JSONHandler
     */
    protected $handler;

    /**
     * @var string
     */
    protected $file;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->file = resource_path('lang/en.json');
        $this->handler = new JSONHandler($this->file);
    }

    public function test_getTranslations_should_return_a_translation_collection()
    {
        $this->assertEquals(TranslationCollection::class, get_class($this->handler->getTranslations()));
    }

    public function test_getTranslations_should_return_the_translation_in_dot_format()
    {
        $translations = (new PHPHandler(resource_path('lang/en/validation.php')))->getTranslations();
        $this->printTranslationsToJSON($this->file, $translations);

        $this->assertEquals(
            $translations,
            $this->handler->getTranslations()
        );

        $this->filesystem->delete($this->file);
    }

    public function test_getTranslations_should_returns_empty_collection_when_the_file_does_not_exist()
    {
        $this->handler = new JSONHandler(resource_path('lang/en/'. $this->faker->word.'.json'));

        $this->assertEquals(
            new TranslationCollection(),
            $this->handler->getTranslations()
        );
    }

    /**
     * Save the translations into a file in JSON format
     *
     * @param string $path
     * @param TranslationCollection $translations
     */
    private function printTranslationsToJSON(string $path, TranslationCollection $translations)
    {
        $content = [];

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            $content[$translation->getPath()] = $translation->getTranslation();
        }

        $json = json_encode($content, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

        file_put_contents($path, $json);
    }
}