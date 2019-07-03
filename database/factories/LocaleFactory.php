<?php

use Faker\Generator as Faker;

$factory->define(\Kodilab\LaravelI18n\Models\Locale::class, function (Faker $faker) {

    $language_iso = $faker->unique()->languageCode;

    return [
        'iso' => $language_iso,
        'region' => $faker->unique()->countryCode,
        'description' => $faker->text,
        'laravel_locale' => $language_iso,
        'currency_number_decimals' => 2,
        'currency_decimals_punctuation' => '.',
        'currency_thousands_separator' => null,
        'currency_symbol' => $faker->currencyCode,
        'currency_symbol_position' => 'after',
        'carbon_locale' => $language_iso,
        'tz' => $faker->timezone,
        'enabled' => $faker->boolean,
        'fallback' => false
    ];
});