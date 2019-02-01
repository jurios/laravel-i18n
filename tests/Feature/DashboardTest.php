<?php

namespace Kodilab\LaravelI18n\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Kodilab\LaravelI18n\Facade;
use Kodilab\LaravelI18n\Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp()
    {
        parent::setUp(); // TODO: Change the autogenerated stub
    }

    public function test_dashboard_is_working()
    {
        Facade::editorRoutes();

        $this->get(route('i18n.dashboard'))
            ->assertStatus(200)->assertViewIs('i18n::dashboard.dashboard');
    }

    public function test_dashboard_custom_view_is_loaded()
    {
        Facade::editorRoutes([
            'I18nDashboardController@dashboard' => $this->faker->word
        ]);

        //It should return 500 as the view doesn't exists
        $this->get(route('i18n.dashboard'))->assertStatus(500);
    }
}
