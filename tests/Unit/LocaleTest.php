<?php


namespace Kodilab\LaravelI18n\Tests\Unit;


use Kodilab\LaravelI18n\Tests\Unit\Translations\TestCase;

class LocaleTest extends TestCase
{
    public function test_fallback_locale_can_not_be_desabled()
    {
        $this->fallback_locale->enabled = false;
        $this->fallback_locale->save();

        $this->assertTrue($this->fallback_locale->enabled);
    }
}