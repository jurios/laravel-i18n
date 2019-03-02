<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackageTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('test_models', function (Blueprint $table) {
            $table->increments('id');
        });

        /*\Kodilab\LaravelI18n\Facade::generateModelI18nTable('test_models', [
            'name' => 'string',
            'description' => 'text'
        ]);*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //\Kodilab\LaravelI18n\Facade::dropIfExistsModelI18nTable('test_models');
        Schema::dropIfExists('test_models');
    }
}
