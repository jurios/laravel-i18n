<?php


namespace Kodilab\LaravelI18n\Tests\Unit\Models\Translation;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\Traits\InstallPackage;
use Kodilab\LaravelI18n\Tests\Unit\Traits\HasTranslations\Fixtures\Models\TranslatableModel;
use Kodilab\LaravelI18n\Tests\TestCase;

class HasTranslationsTest extends TestCase
{
    use WithFaker, InstallPackage;

    /** @var TranslatableModel */
    protected $model;

    /** @var Locale */
    protected $locale;

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/Fixtures/database/migrations');
        $this->withFactories(__DIR__ . '/Fixtures/database/factories');

        $this->model = factory(TranslatableModel::class)->create();
        $this->locale = factory(Locale::class)->create();
    }

    public function test_setTranslatedAttributes_should_save_a_translation()
    {
        $value = $this->faker->paragraph;
        $this->model->setTranslatedAttributes($this->locale, [
            'field' => $value
        ]);

        $this->assertEquals($value,
            DB::table('translatable_model_translations')
                ->where('locale_id', $this->locale->id)
                ->where('model_id', $this->model->id)->get()->first()->field
        );
    }

    public function test_setTranslatedAttribute_should_save_an_attribute_translation()
    {
        $value = $this->faker->paragraph;
        $this->model->setTranslatedAttribute($this->locale, 'field', $value);

        $this->assertEquals($value,
            DB::table('translatable_model_translations')
                ->where('locale_id', $this->locale->id)
                ->where('model_id', $this->model->id)->get()->first()->field
        );
    }

    public function test_getTranslatedAttribute_should_return_the_translated_attribute()
    {
        $value = $this->faker->paragraph;

        $this->model->setTranslatedAttributes($this->locale, [
            'field' => $value
        ]);

        $this->assertEquals($value, $this->model->getTranslatedAttribute($this->locale, 'field'));
    }

    public function test_getTranslatedAttribute_of_an_non_translatable_attribute_should_return_null()
    {
        $this->assertNull($this->model->getTranslatedAttribute($this->locale, $this->faker->word));
    }

    public function test_dynamically_translated_attributes_are_generated()
    {
        $locale = factory(Locale::class)->create();

        app('i18n')->setLocale($locale);

        $value = $this->faker->paragraph;

        $this->model->setTranslatedAttributes($locale, [
            'field' => $value
        ]);

        $this->assertEquals($value, $this->model->field);
    }

    public function test_dynamically_translated_attributes_should_return_the_locale_setting_translation()
    {
        //TODO: Add service provider into the application configuration file
        $i18n = app('i18n');

        $translations = [
            $this->locale->reference => $this->faker->unique()->paragraph,
            $this->app['i18n']->getLocale()->reference => $this->faker->unique()->paragraph
        ];

        $this->model->setTranslatedAttribute(
            $this->app['i18n']->getLocale(), 'field', $translations[$this->app['i18n']->getLocale()->reference]
        );

        $this->model->setTranslatedAttribute(
            $this->locale, 'field', $translations[$this->locale->reference]
        );

        $this->assertEquals($translations[$this->app['i18n']->getLocale()->reference], $this->model->field);
        $this->app['i18n']->setLocale($this->locale);
        $this->assertEquals($translations[$this->locale->reference], $this->model->field);
    }

    public function test_isTranslated_should_return_whether_it_has_been_translated_or_not()
    {
        $this->assertFalse($this->model->isTranslated($this->locale, 'field'));

        $this->model->setTranslatedAttributes($this->locale, [
            'field' => $this->faker->paragraph
        ]);

        $this->assertTrue($this->model->isTranslated($this->locale, 'field'));
    }
}