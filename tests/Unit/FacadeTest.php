<?php

namespace Kodilab\LaravelI18n\Tests\Unit;

use Illuminate\Database\QueryException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Kodilab\LaravelI18n\Facade;
use Kodilab\LaravelI18n\Tests\TestCase;

class FacadeTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

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

        Facade::generateModelI18nTable('test_models', $attributes);

        $this->assertTrue(Schema::hasTable('test_models_i18n'));

        $columns = Schema::getColumnListing('test_models_i18n');

        foreach ($attributes as $name => $value)
        {
            $this->assertContains($name, $columns);
        }

        Facade::dropIfExistsModelI18nTable('test_models');

        $this->expectException(QueryException::class);

        DB::table('test_models_i18n')->get();
    }
}
