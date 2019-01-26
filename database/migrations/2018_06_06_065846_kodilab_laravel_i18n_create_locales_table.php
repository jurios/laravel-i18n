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

            $table->string('ISO_639_1')->nullable(false);
            $table->string('region')->nullable(true)->default(null);

            $table->text('description')->nullable(true)->default(null);

            $table->unsignedInteger('dialect_of_id')->nullable(true)->default(null);
            $table->foreign('dialect_of_id')->references('id')->on(config('i18n.tables.locales'))
                ->onDelete('set null');

            $table->string('laravel_locale')->nullable(true)->default(null);

            $table->string('price_format')->nullable(true)->default(null);
            $table->string('price_symbol')->nullable(true)->default(null);
            $table->enum('price_position', ['after', 'before'])->nullable(true)->default('after');

            $table->string('carbon_locale')->nullable(true)->default(null);
            $table->string('carbon_tz')->nullable(true)->default(null);

            $table->boolean('enabled')->nullable(false)->default(true);
            $table->boolean('fallback')->nullable(false)->default(false);
            $table->boolean('created_by_sync')->nullable(false)->default(false);

            $table->timestamps();
        });

        Schema::table(config('i18n.tables.translations'), function (Blueprint $table) {
            $table->unsignedInteger('locale_id')->nullable(false)->default(0)->after('needs_revision');
            $table->foreign('locale_id')->references('id')->on(config('i18n.tables.locales'))
                ->onDelete('cascade');
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
