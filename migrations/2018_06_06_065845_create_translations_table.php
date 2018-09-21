<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('i18n.tables.translations'), function (Blueprint $table) {
            $table->increments('id');

            $table->string('md5')->nullable(false);

            $table->text('translation')->nullable(true);

            $table->boolean('needs_revision')->nullable(false)->default(false);

            $table->unsignedInteger('language_id')->nullable(false);
            $table->foreign('language_id')->references('id')->on(config('i18n.tables.languages'))
                ->onDelete('cascade');

            $table->unsignedInteger('text_id')->nullable(true)->default(null);
            $table->foreign('text_id')->references('id')->on(config('i18n.tables.texts'))
                ->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('i18n.tables.translations'));
    }
}
