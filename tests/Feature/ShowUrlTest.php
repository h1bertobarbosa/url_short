<?php

namespace Tests\Feature;

use App\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ShowUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldBeReturnErrorIfRegisterNotExist()
    {
        $response = $this->getJson("/urls/99999");

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Data does not exist.'
            ]);
    }

    public function testShouldBeReturnExistData()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $response = $this->getJson("/urls/{$url->url_code}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_name',
                'original_url',
                'url_code',
                'clicks'
            ]);
    }
}
