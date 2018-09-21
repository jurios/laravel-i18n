<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTextsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('i18n.tables.texts'), function (Blueprint $table) {
            $table->increments('id');

            $table->string('md5')->nullable(false);
            $table->text('text')->nullable(false);

            $table->text('paths')->nullable(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('i18n.tables.texts'));
    }
}
