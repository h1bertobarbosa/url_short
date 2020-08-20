<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldErrorIfNotHeaderUser()
    {
        $expectedUrl = 'https://rhizom.me/';
        $expectedCode = 'code123';

        $response = $this->postJson("/urls", [
            'original_url' => $expectedUrl,
            'url_code' => $expectedCode
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => 'User is required'
            ]);
    }

    public function testStore()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->postJson('/urls', ['url' => 'https://rhizom.me/']);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'user_name',
                'original_url',
                'url_code',
                'clicks'
            ]);
    }

    public function testReturnValidationMessageIfNotSendUrl()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->postJson('/urls', ['url' => '']);

        $response
            ->assertStatus(400)
            ->assertJson([
                "url" => ["The url field is required."]
            ]);
    }

    public function testReturnValidationMessageIfUrlInvalid()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->postJson('/urls', ['url' => 'asdsad']);

        $response
            ->assertStatus(400)
            ->assertJson([
                "url" => ["The url is not a valid URL."]
            ]);
    }

    public function testUrlCodeShouldBe16Caracters()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->postJson('/urls', ['url' => 'https://rhizom.me/']);
        $data = $response->decodeResponseJson();
        $expectedNumberOfCaracter = 16;
        $this->assertEquals($expectedNumberOfCaracter, strlen($data['url_code']));
    }

}