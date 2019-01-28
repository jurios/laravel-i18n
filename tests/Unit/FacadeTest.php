<?php

namespace Kodilab\LaravelI18n\Tests\Unit;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kodilab\LaravelI18n\Facade;
use Kodilab\LaravelI18n\Tests\TestCase;

class FacadeTest extends TestCase
{
    use WithFaker;

    protected $filesystem;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function test_i18n_table_for_test_model_does_not_exists()
    {
        $this->expectException(QueryException::class);

        DB::table('test_models_i18n')->get();
    }

    public function test_generate_i18n_table_for_test_model()
    {
        $attributes = [
            'string' => 'string',
            'number' => 'unsignedInteger',
            'text' => 'text'
        ];

        Facade::generateModelI18nTable($this->test_model_table, $attributes);

        $this->assertTrue(Schema::hasTable($this->test_model_table . config('i18n.tables.model_translations_suffix')));

        $columns = Schema::getColumnListing($this->test_model_table . config('i18n.tables.model_translations_suffix'));

        foreach ($attributes as $name => $value)
        {
            $this->assertContains($name, $columns);
        }

        Facade::dropIfExistsModelI18nTable($this->test_model_table);

        $this->expectException(QueryException::class);

        DB::table($this->test_model_table . config('i18n.tables.model_translations_suffix'))->get();
    }
}