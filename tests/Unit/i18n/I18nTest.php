<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n;


use Kodilab\LaravelI18n\i18n\i18n;
use Kodilab\LaravelI18n\Tests\TestCase;

class I18nTest extends TestCase
{
    public function test_generateName_should_generate_a_locale_name()
    {
        $iso = 'en';
        $region = 'GB';

        $this->assertEquals('en_GB', i18n::generateName($iso, $region));
        $this->assertEquals('en', i18n::generateName($iso));
        $this->assertEquals('en', i18n::generateName(mb_strtoupper($iso)));
        $this->assertEquals('en_GB', i18n::generateName($iso, mb_strtolower($region)));
    }
}