<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class HelpersTest extends TestCase
{
    use WithFaker;

    public function test_helper_number_should_return_localized_value()
    {
        $locale = factory(Locale::class)->create([
            'decimals' => 2,
            'decimals_punctuation' => ';',
            'thousands_separator' => ':'
        ]);

        $this->app['config']->set('app.locale', $locale->reference);

        $this->assertEquals("1:111;10", __number(1111.1));
    }

    public function test_helper_price_should_return_localized_value_and_currency_symbol_after()
    {
        $locale = factory(Locale::class)->create([
            'decimals' => 2,
            'decimals_punctuation' => ';',
            'thousands_separator' => ':',
            'currency_symbol_position' => 'after',
            'currency_symbol' => '€'
        ]);

        $this->app['config']->set('app.locale', $locale->reference);

        $this->assertEquals("1:111;10 €", __price(1111.1));
    }

    public function test_helper_price_should_return_localized_value_and_currency_symbol_before()
    {
        $locale = factory(Locale::class)->create([
            'decimals' => 2,
            'decimals_punctuation' => ';',
            'thousands_separator' => ':',
            'currency_symbol_position' => 'before',
            'currency_symbol' => '€'
        ]);

        $this->app['config']->set('app.locale', $locale->reference);

        $this->assertEquals("€ 1:111;10", __price(1111.1));
    }

    public function test_helper_price_should_return_localized_value_only_when_currency_symbol_is_null()
    {
        $locale = factory(Locale::class)->create([
            'decimals' => 2,
            'decimals_punctuation' => ';',
            'thousands_separator' => ':',
            'currency_symbol_position' => 'before',
            'currency_symbol' => null
        ]);

        $this->app['config']->set('app.locale', $locale->reference);

        $this->assertEquals("1:111;10", __price(1111.1));
    }
}