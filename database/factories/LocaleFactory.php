<?php

use Faker\Generator as Faker;

$factory->define(\Kodilab\LaravelI18n\Models\Locale::class, function (Faker $faker) {

    $language = $faker->unique()->languageCode;

    return [
        'language' => $language,
        'region' => $faker->unique()->countryCode,

        'name' => $faker->word,

        'fallback' => false,

        'laravel_locale' => $language,
        'carbon_locale' => $language,
        'tz' => $faker->timezone,

        'decimals' => 2,
        'decimals_punctuation' => '.',
        'thousands_separator' => null,
        'currency_symbol' => $faker->currencyCode,
        'currency_symbol_position' => 'after'

    ];
});