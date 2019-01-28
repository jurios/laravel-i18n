<?php

namespace Kodilab\LaravelI18n\Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Models\Translation;
use Kodilab\LaravelI18n\Tests\TestCase;

class HelpersTest extends TestCase
{
    use WithFaker;

    protected function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        factory(Locale::class)->create([
            'fallback' => true,
            'enabled' => true
        ]);
    }

    public function test_t_function_is_working()
    {
        $text = $this->faker->text;

        factory(Translation::class)->create([
            'md5' => md5($text),
            'translation' => $text
        ]);

        $translated_text = t($text);

        $this->assertEquals($text, $translated_text);
    }
}
