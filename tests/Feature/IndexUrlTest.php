<?php

namespace Tests\Feature;

use App\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class IndexUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShoulBeRedirectToOriginalUrlWhenRouteIsAccessWithUrlCode()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $response = $this->getJson("/{$url->url_code}");

        $response
            ->assertStatus(301)
            ->assertRedirect($url->original_url);
    }

    public function testShoulBeCountNumberOfAccess()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $this->getJson("/{$url->url_code}");
        $this->getJson("/{$url->url_code}");
        $this->getJson("/{$url->url_code}");
        $this->getJson("/{$url->url_code}");

        $numberOfAccess = 4;
        $urlAfterAccess = Url::find($url->id);
        $this->assertEquals($numberOfAccess, $urlAfterAccess->clicks);
    }

    public function testShouldBeReturnErrorIfRegisterNotExist()
    {
        $response = $this->getJson("/code321564");

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Data does not exist.'
            ]);
    }
}
