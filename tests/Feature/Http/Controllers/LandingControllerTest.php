<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class LandingControllerTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_page_opens_on_oreo_matcha_with_a_drink_dropdown(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertOk()
            ->assertSee('id="pickBtn"', false)
            ->assertSee('<span id="pickVal">Oreo Matcha</span>', false)
            ->assertSee('Iced Oreo Matcha');
    }

    public function test_home_page_offers_all_four_drinks_with_oreo_matcha_as_the_default(): void
    {
        $response = $this->get(route('home'));

        $response
            ->assertSee("label: 'Oreo Matcha'", false)
            ->assertSee("label: 'Mocha Latte'", false)
            ->assertSee("label: 'Strawberry Matcha'", false)
            ->assertSee("label: 'Sea Salt'", false)
            ->assertSee("var current = 'oreo';", false);
    }
}
