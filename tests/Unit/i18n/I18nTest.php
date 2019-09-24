<?php


namespace Kodilab\LaravelI18n\Tests\Unit\i18n;


use Kodilab\LaravelI18n\i18n\i18n;
use Kodilab\LaravelI18n\Tests\TestCase;

class I18nTest extends TestCase
{
    public function test_generateReference_should_generate_a_locale_reference()
    {
        $language = 'en';
        $region = 'GB';

        $this->assertEquals('en_GB', i18n::generateReference($language, $region));
        $this->assertEquals('en', i18n::generateReference($language));
        $this->assertEquals('en', i18n::generateReference(mb_strtoupper($language)));
        $this->assertEquals('en_GB', i18n::generateReference($language, mb_strtolower($region)));
    }

    public function test_isReferenceValid_should_return_true_if_the_reference_is_valid()
    {
        $this->assertTrue(i18n::isReferenceValid('en_GB'));
        $this->assertTrue(i18n::isReferenceValid('enn_GB'));
        $this->assertTrue(i18n::isReferenceValid('en_GBB'));
        $this->assertTrue(i18n::isReferenceValid('en'));

        $this->assertFalse(i18n::isReferenceValid('_GB'));
        $this->assertFalse(i18n::isReferenceValid('GB'));
        $this->assertFalse(i18n::isReferenceValid('gB'));

        $this->assertFalse(i18n::isReferenceValid('EN_gb'));
        $this->assertFalse(i18n::isReferenceValid('EN'));
        $this->assertFalse(i18n::isReferenceValid('en_gb'));
        $this->assertFalse(i18n::isReferenceValid('eenn_gb'));
    }

    public function test_getLanguage_should_return_the_language_from_a_reference()
    {
        $name = 'en_GB';

        $this->assertEquals('en', i18n::getLanguage($name));
    }

    public function test_getLanguage_with_an_invalid_reference_should_throw_an_exception()
    {
        $this->expectException(\InvalidArgumentException::class);
        $name = '_GB';

        i18n::getLanguage($name);
    }

    public function test_getRegion_should_return_the_region_from_a_reference()
    {
        $name = 'en_GB';
        $this->assertEquals('GB', i18n::getRegion($name));
    }

    public function test_getRegion_should_return_null_if_the_reference_does_not_have_a_region()
    {
        $name = 'en';
        $this->assertNull(i18n::getRegion($name));
    }

    public function test_getRegion_should_throw_an_exception_if_the_reference_is_not_valid()
    {
        $this->expectException(\InvalidArgumentException::class);

        i18n::getRegion('en_gb');
    }
}