<?php


namespace Kodilab\LaravelI18n\Tests\Commands;


use Kodilab\LaravelI18n\Models\Locale;
use Kodilab\LaravelI18n\Tests\TestCase;

class SyncCommandTest extends TestCase
{
    public function test_install_command_will_create_a_fallback_locale()
    {
        $this->artisan('i18n:sync')->run();
    }
}