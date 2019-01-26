<?php

use Faker\Generator as Faker;

$factory->define(\Kodilab\LaravelI18n\Models\Translation::class, function (Faker $faker) {
    $text = $faker->text;

    $values = [
        'translation' => $text,
        'needs_revision' => $faker->boolean,
        'text_id' => function () use ($text) {
            return factory(\Kodilab\LaravelI18n\Models\Text::class)->create(
                ['text' => $text]
            );
        },
        'locale_id' => function () {
            return factory(\Kodilab\LaravelI18n\Models\Locale::class)->create();
        },
        'md5' => null
    ];

    $values['md5'] = md5($values['translation']);

    return $values;
});