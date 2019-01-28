<?php

use Faker\Generator as Faker;

$factory->define(\Kodilab\LaravelI18n\Models\Text::class, function (Faker $faker) {

    if (\Kodilab\LaravelI18n\Models\Locale::where('fallback', true)->get()->isEmpty())
    {
        factory(\Kodilab\LaravelI18n\Models\Locale::class)->create([
            'fallback' => true,
            'enabled' => true
        ]);
    }

    $values = [
        'text' => $faker->text,
        'paths' => [
            $faker->word . DIRECTORY_SEPARATOR . $faker->word => $faker->numberBetween()
        ],
        'md5' => null
    ];

    $values['md5'] = md5($values['text']);

    return $values;
});