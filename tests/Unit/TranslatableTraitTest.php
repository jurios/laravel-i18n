<?php

namespace Kodilab\LaravelI18n\Tests\Unit;


use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Facade;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\Resources\TestModels\TestModel;
use Kodilab\LaravelI18n\Tests\TestCase;

class TranslatableTraitTest extends TestCase
{

    const TRANSLATABLE_FIELDS = [
        'name' => 'string',
        'description' => 'text'
    ];

    /** @var TestModel $model */
    protected $model;

    /** @var Locale $locale */
    protected $locale;

    use WithFaker;
    use RefreshDatabase;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        Facade::generateModelI18nTable($this->test_model_table, self::TRANSLATABLE_FIELDS);

        $this->model = factory(TestModel::class)->create();

        $this->fallback_locale = $this->generateFallbackLocale();
        $this->locale = factory(Locale::class)->create(['enabled' => true]);

    }

    protected function tearDown()
    {
        Facade::dropIfExistsModelI18nTable($this->test_model_table);

        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    public function test_translatable_attributes_are_accessible()
    {
        foreach (self::TRANSLATABLE_FIELDS as $field => $type) {
            $this->assertNotNull($this->model->$field);
        }

        $name = $this->faker->word;
        $description = $this->faker->paragraph;

        $this->model->updateTranslation(['name' => $name, 'description' => $description], $this->fallback_locale);


        $this->assertEquals($name, $this->model->name);
        $this->assertEquals($description, $this->model->description);
    }

    public function test_no_translation_available_returns_empty_string()
    {
        foreach (self::TRANSLATABLE_FIELDS as $field => $type) {
            $this->assertEquals('', $this->model->$field);
        }
    }

    public function test_get_translated_attribute_returns_the_locale_translation()
    {
        $name = $this->faker->word;
        $description = $this->faker->paragraph;

        $this->model->updateTranslation(['name' => $name, 'description' => $description], $this->locale);

        $this->assertEquals($name, $this->model->getTranslatedAttribute('name', $this->locale));
        $this->assertEquals($description, $this->model->getTranslatedAttribute('description', $this->locale));
    }

    public function test_update_translation_update_a_translation()
    {
        $name = $this->faker->word;
        $this->model->updateTranslation(['name' => $name], $this->locale);

        $this->assertEquals($name, $this->model->getTranslatedAttribute('name', $this->locale));

        $name = $this->faker->word;
        $this->model->updateTranslation(['name' => $name], $this->locale);

        $this->assertEquals($name, $this->model->getTranslatedAttribute('name', $this->locale));
    }

    public function test_call_translated_attribute_returns_the_translation()
    {
        $name = $this->faker->word;
        $this->model->updateTranslation(['name' => $name], $this->locale);

        $this->assertEquals($name, $this->model->getTranslatedAttribute('name', $this->locale));
        $this->assertEquals($name, $this->model->name($this->locale));
    }
}