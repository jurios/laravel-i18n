<?php

use Faker\Generator as Faker;

$factory->define(\Kodilab\LaravelI18n\Models\Locale::class, function (Faker $faker) {

    $language = $faker->unique()->languageCode;

    return [
        'language' => $language,
        'region' => $faker->unique()->countryCode,

        'name' => $faker->word,
        'description' => $faker->text,

        'laravel_locale' => $language,
        'currency_number_decimals' => 2,
        'currency_decimals_punctuation' => '.',
        'currency_thousands_separator' => null,
        'currency_symbol' => $faker->currencyCode,
        'currency_symbol_position' => 'after',
        'carbon_locale' => $language,
        'tz' => $faker->timezone,
        'enabled' => $faker->boolean
    ];
});