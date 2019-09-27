<?php

use Faker\Generator as Faker;
use Kodilab\LaravelI18n\i18n\i18n;

$factory->define(\Kodilab\LaravelI18n\Models\Locale::class, function (Faker $faker) {

    $language = $faker->unique()->languageCode;
    $country = $faker->unique()->countryCode;

    return [
        'language' => $language,
        'region' => $country,

        'name' => $faker->word,

        'fallback' => false,

        'laravel_locale' => i18n::generateReference($language, $country),
        'carbon_locale' => $language,
        'tz' => $faker->timezone,

        'decimals' => 2,
        'decimals_punctuation' => '.',
        'thousands_separator' => null,
        'currency_symbol' => $faker->currencyCode,
        'currency_symbol_position' => 'after'

    ];
});