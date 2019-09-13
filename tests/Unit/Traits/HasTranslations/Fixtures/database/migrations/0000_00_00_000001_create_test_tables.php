<?php


use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTestTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('translatable_models', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->timestamps();
        });

        Schema::create('translatable_model_translations', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->text('field')->nullable(false)->default(null);

            $table->unsignedBigInteger('model_id')->unique()->nullable(false)->default(null);
            $table->foreign('model_id')->references('id')->on('translatable_models')->onDelete('cascade');

            $table->unsignedBigInteger('locale_id')->unique()->nullable(false)->default(null);
            $table->foreign('locale_id')->references('id')->on(config('i18n.tables.locales'))->onDelete('cascade');

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
        Schema::dropIfExists('model_ones');
        Schema::dropIfExists('model_one_translations');
    }
}
