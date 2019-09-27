<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\i18n\FileHandlers\JSONHandler;
use Kodilab\LaravelI18n\i18n\Translations\Translation;
use Kodilab\LaravelI18n\i18n\Translations\TranslationCollection;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;

class SyncTest extends TestCase
{
    use WithFaker, InstallPackage;

    public function test_sync_will_create_a_fallback_locale_json_file()
    {
        $this->removePublishedTranslations();
        $locale = Locale::getFallbackLocale();

        $this->assertFalse($this->filesystem->exists(resource_path("lang/{$locale->reference}.json")));
        $this->artisan('i18n:sync')->run();
        $this->assertTrue($this->filesystem->exists(resource_path("lang/{$locale->reference}.json")));
    }

    public function test_sync_will_create_other_locales_json_files()
    {
        $locale = factory(Locale::class)->create();

        $this->assertFalse($this->filesystem->exists(resource_path('lang/' . $locale->reference . '.json')));
        $this->artisan('i18n:sync')->run();
        $this->assertTrue($this->filesystem->exists(resource_path('lang/' . $locale->reference . '.json')));

    }

    public function test_sync_no_fallback_locale_should_generate_empty_translations()
    {
        $locale = factory(Locale::class)->create();
        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))->getTranslations();

        $empty = true;

        /** @var Translation $translation */
        foreach ($translations as $translation) {
            if (!$translation->isEmpty()) {
                $empty = false;
            }
        }

        $this->assertTrue($empty);
    }

    public function test_sync_should_import_paths_from_the_php_files_even_if_the_locale_does_not_exists()
    {
        //Transfer fallback to new locale and remove 'en' locale as there are 'en' php translation files
        $locale = factory(Locale::class)->create();
        Locale::getFallbackLocale()->delete();
        $this->app['config']->set('app.fallback_locale', $locale->reference);


        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))->getTranslations();

        $this->assertFalse($translations->where('path', 'validation.accepted')->isEmpty());
    }

    public function test_sync_should_keep_indexed_translations()
    {
        // Generate fallback locale JSON file
        $this->artisan('i18n:sync')->run();

        $translations = (new JSONHandler(resource_path('lang/' . $this->fallback_locale->reference . '.json')))->getTranslations();

        /** @var Translation $accepted_validation */
        $accepted_validation = $translations->where('path', 'validation.accepted')->first();

        $locale = factory(Locale::class)->create();

        $path = $accepted_validation->getPath();
        $translation = $this->faker->paragraph;

        (new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))->save(new TranslationCollection([new Translation($path, $translation)]));

        $this->artisan('i18n:sync')->run();

        /** @var Translation $persisted_translation */
        $persisted_translation = (new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))
            ->getTranslations()
            ->where('path', $path)->first();

        $this->assertEquals($translation, $persisted_translation->getTranslation());
    }

    public function test_sync_should_remove_deprecated_translations()
    {
        $locale = factory(Locale::class)->create();

        $path = $this->faker->word;
        $translation = $this->faker->paragraph;

        (new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))->save(new TranslationCollection([new Translation($path, $translation)]));

        $this->artisan('i18n:sync')->run();

        $this->assertTrue((new JSONHandler(resource_path('lang/' . $locale->reference . '.json')))
            ->getTranslations()
            ->where('path', $path)->isEmpty()
        );
    }
}