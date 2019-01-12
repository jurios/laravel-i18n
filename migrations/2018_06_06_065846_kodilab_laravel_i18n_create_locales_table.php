<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KodilabLaravelI18nCreateTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('i18n.tables.locales'), function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('language_id')->nullable(false);

            $table->foreign('language_id')->references('id')->on(config('i18n.tables.languages'))
                ->onDelete('cascade');

            $table->string('price_format');
            $table->string('price_symbol');
            $table->enum('price_position', ['after', 'before']);

            $table->string('carbon_locale');

            $table->boolean('enabled')->nullable(false)->default(true);
            $table->boolean('fallback')->nullable(false)->default(false);
            $table->boolean('created_by_sync')->nullable(false)->default(false);

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
        Schema::dropIfExists(config('i18n.tables.locales'));
    }
}
