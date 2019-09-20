<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\i18n\FileHandlers\PHPHandler;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\i18n\Sync\LocaleSync;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class TranslationManagerTest extends TestCase
{
    use WithFaker;

    /** @var Locale */
    protected $locale;

    /** @var LocaleSync */
    protected $manager;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->locale = Locale::getFallbackLocale();
        $this->manager = new LocaleSync($this->locale);
    }

    public function test_php_should_returns_a_translation_collection_from_the_php_translations()
    {
        /** @var TranslationCollection $translations */
        $translations = $this->manager->php();

        $cont = 0;
        foreach (array_keys($translations->toRaw()) as $path) {
            if (preg_match('/^validation\./', $path)) {
                $cont++;
            }
        }

        $this->assertEquals(
            $cont,
            count((new PHPHandler(resource_path('lang/en/validation.php')))->getTranslations())
        );
    }

    public function test_php_should_return_an_empty_collection_if_the_path_does_not_exist()
    {
        $locale = factory(Locale::class)->create();

        $translations = (new LocaleSync($locale))->php();

        $this->assertTrue($translations->isEmpty());
    }

    public function test_json_should_returns_a_translation_collection_from_the_json_translations()
    {
        $translations = (new PHPHandler(resource_path('lang/en/validation.php')))->getTranslations();

        $this->printTranslationsToJSON(resource_path('lang/en.json'), $translations);

        $this->assertEquals(
            (new JSONHandler(resource_path('lang/en.json')))->getTranslations(),
            $this->manager->json()
        );

        $this->filesystem->delete(resource_path('lang/en.json'));
    }

    public function test_merge_should_return_a_merge_of_php_translations_and_json_translations()
    {
        $json_translations = (new TranslationCollection())->add(new Translation($this->faker->word, $this->faker->paragraph));

        $this->printTranslationsToJSON(resource_path('lang/en.json'), $json_translations);

        /** @var TranslationCollection $translations */
        $translations = $this->manager->merge();

        $this->assertTrue(in_array($json_translations->first()->getPath(), array_keys($translations->toRaw())));

        $this->filesystem->delete(resource_path('lang/en.json'));
    }
}