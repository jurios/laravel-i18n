<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLanguagesData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $languages = include(__DIR__ . '/../languages_generator.php');

        foreach ($languages as $language)
        {
            DB::table(config('i18n.tables.languages'))->insert([
                'name' => mb_convert_case($language["name"], MB_CASE_TITLE, 'UTF-8'),
                'ISO_639_1' => $language["ISO_639_1"],
                'enabled' => false
            ]);
        }

        DB::table(config('i18n.tables.languages'))->where('ISO_639_1', config('app.locale'))->update([
            'enabled' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table(config('i18n.tables.languages'))->delete();
    }
}
