<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class KodilabLaravelI18nCreateLocalesTable extends Migration
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

            $table->string('iso')->nullable(false);
            $table->string('region')->nullable(true);
            $table->index(['iso', 'region']);

            $table->text('description')->nullable(true)->default(null);

            $table->string('laravel_locale')->nullable(true)->default(null);

            $table->unsignedInteger('currency_number_decimals')->nullable(true)->default(null);
            $table->char('currency_decimals_punctuation')->nullable(true)->default(null);
            $table->char('currency_thousands_separator')->nullable(true)->default(null);
            $table->string('currency_symbol')->nullable(true)->default(null);
            $table->enum('currency_symbol_position', ['after', 'before'])->nullable(false)->default('after');

            $table->string('carbon_locale')->nullable(true)->default(null);
            $table->string('tz')->nullable(true)->default(null);

            $table->boolean('enabled')->nullable(false)->default(false);
            $table->boolean('fallback')->nullable(false)->default(false);

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
