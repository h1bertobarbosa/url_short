<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UrlTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testStore()
    {
        $response = $this->postJson('/urls', ['url' => 'https://rhizom.me/']);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'original_url',
                'url_code'
            ]);
    }

    public function testReturnValidationMessageIfNotSendUrl()
    {
        $response = $this->postJson('/urls', ['url' => '']);

        $response
            ->assertStatus(400)
            ->assertJson([
                "url" => ["The url field is required."]
            ]);
    }

    public function testReturnValidationMessageIfUrlInvalid()
    {
        $response = $this->postJson('/urls', ['url' => 'asdsad']);

        $response
            ->assertStatus(400)
            ->assertJson([
                "url" => ["The url is not a valid URL."]
            ]);
    }

    public function testUrlCodeShouldBe16Caracters()
    {
        $response = $this->postJson('/urls', ['url' => 'https://rhizom.me/']);
        $data = $response->decodeResponseJson();
        $expectedNumberOfCaracter = 16;
        $this->assertEquals($expectedNumberOfCaracter, strlen($data['url_code']));
    }
}
