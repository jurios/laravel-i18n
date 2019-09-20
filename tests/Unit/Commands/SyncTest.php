<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class SyncTest extends TestCase
{
    use WithFaker;

    public function test_sync_will_create_a_fallback_locale_json_file()
    {
        $this->assertFalse($this->filesystem->exists(resource_path('lang/en.json')));
        $this->artisan('i18n:sync')->run();
        $this->assertTrue($this->filesystem->exists(resource_path('lang/en.json')));
    }

    public function test_sync_will_create_other_locales_json_files()
    {
        $locale = factory(Locale::class)->create();

        $this->assertFalse($this->filesystem->exists(resource_path('lang/' . $locale->name . '.json')));
        $this->artisan('i18n:sync')->run();
        $this->assertTrue($this->filesystem->exists(resource_path('lang/' . $locale->name . '.json')));

    }

    public function test_sync_fallback_locale_should_not_has_empty_translations()
    {
        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $this->fallback_locale->name . '.json')))->getTranslations();

        $empty = false;

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            if ($translation->isEmpty()) {
                $empty = true;
            }
        }

        $this->assertFalse($empty);
    }

    public function test_sync_no_fallback_locale_should_generate_empty_translations()
    {
        $locale = factory(Locale::class)->create();
        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $locale->name . '.json')))->getTranslations();

        $empty = true;

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            if (!$translation->isEmpty()) {
                $empty = false;
            }
        }

        $this->assertTrue($empty);
    }

    public function test_sync_should_keep_indexed_translations()
    {
        // Generate fallback locale JSON file
        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $this->fallback_locale->name . '.json')))->getTranslations();

        /** @var Translation $accepted_validation */
        $accepted_validation = $translations->where('path', 'validation.accepted')->first();

        $locale = factory(Locale::class)->create();

        $path = $accepted_validation->getPath();
        $translation = $this->faker->paragraph;

        (new JSONHandler(resource_path('lang/' . $locale->name . '.json')))->save(new TranslationCollection([new Translation($path, $translation)]));

        $this->artisan('i18n:sync')->run();

        /** @var Translation $persisted_translation */
        $persisted_translation = (new JSONHandler(resource_path('lang/' . $locale->name . '.json')))
            ->getTranslations()
            ->where('path', $path)->first();

        $this->assertEquals($translation, $persisted_translation->getTranslation());
    }

    public function test_sync_should_remove_deprecated_translations()
    {
        $locale = factory(Locale::class)->create();

        $path = $this->faker->word;
        $translation = $this->faker->paragraph;

        (new JSONHandler(resource_path('lang/' . $locale->name . '.json')))->save(new TranslationCollection([new Translation($path, $translation)]));

        $this->artisan('i18n:sync')->run();

        $this->assertTrue((new JSONHandler(resource_path('lang/' . $locale->name . '.json')))
            ->getTranslations()
            ->where('path', $path)->isEmpty()
        );
    }
}