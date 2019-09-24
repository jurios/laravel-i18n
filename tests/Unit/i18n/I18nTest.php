<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n;


use Kodilab\LaravelI18n\i18n\i18n;
use Kodilab\LaravelI18n\Tests\TestCase;

class I18nTest extends TestCase
{
    public function test_generateName_should_generate_a_locale_name()
    {
        $language = 'en';
        $region = 'GB';

        $this->assertEquals('en_GB', i18n::generateName($language, $region));
        $this->assertEquals('en', i18n::generateName($language));
        $this->assertEquals('en', i18n::generateName(mb_strtoupper($language)));
        $this->assertEquals('en_GB', i18n::generateName($language, mb_strtolower($region)));
    }

    public function test_isNameValid_should_return_true_if_the_name_is_valid()
    {
        $this->assertTrue(i18n::isNameValid('en_GB'));
        $this->assertTrue(i18n::isNameValid('enn_GB'));
        $this->assertTrue(i18n::isNameValid('en_GBB'));
        $this->assertTrue(i18n::isNameValid('en'));

        $this->assertFalse(i18n::isNameValid('_GB'));
        $this->assertFalse(i18n::isNameValid('GB'));
        $this->assertFalse(i18n::isNameValid('gB'));

        $this->assertFalse(i18n::isNameValid('EN_gb'));
        $this->assertFalse(i18n::isNameValid('EN'));
        $this->assertFalse(i18n::isNameValid('en_gb'));
        $this->assertFalse(i18n::isNameValid('eenn_gb'));
    }

    public function test_getISO_should_return_the_language_from_a_name()
    {
        $name = 'en_GB';

        $this->assertEquals('en', i18n::getLanguage($name));
    }

    public function test_getISO_with_an_invalid_name_should_throw_an_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $name = '_GB';

        i18n::getLanguage($name);
    }

    public function test_getRegion_should_return_the_region_from_a_name()
    {
        $name = 'en_GB';
        $this->assertEquals('GB', i18n::getRegion($name));
    }

    public function test_getRegion_should_return_null_if_the_name_does_not_have_a_region()
    {
        $name = 'en';
        $this->assertNull(i18n::getRegion($name));
    }

    public function test_getRegion_should_throw_an_exception_if_the_name_is_not_valid()
    {
        $this->expectException(\InvalidArgumentException::class);

        i18n::getRegion('en_gb');
    }
}