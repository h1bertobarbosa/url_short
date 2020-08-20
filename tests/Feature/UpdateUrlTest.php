<?php

namespace Tests\Feature;

use App\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UpdateUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldErrorIfNotHeaderUser()
    {
        $expectedUrl = 'https://rhizom.me/';
        $expectedCode = 'code123';

        $response = $this->putJson("/urls/{$expectedCode}", [
            'original_url' => $expectedUrl,
            'url_code' => $expectedCode
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'user' => 'User is required'
            ]);
    }

    public function testUpdateUrl()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $expectedUrl = 'https://rhizom.me/';
        $expectedCode = 'code123';
        $response = $this->withHeaders([
            'User-Header' => $url->user_name,
        ])->putJson("/urls/{$url->id}", [
            'original_url' => $expectedUrl,
            'url_code' => $expectedCode
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'user_name',
                'original_url',
                'url_code',
                'clicks'
            ]);

        $updatedData = $response->decodeResponseJson();

        $this->assertEquals($expectedUrl, $updatedData['original_url']);
        $this->assertEquals($expectedCode, $updatedData['url_code']);
    }

    public function testNotBeAbleUpdateUrlIfUserNotOwner()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $expectedUrl = 'https://rhizom.me/';

        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->putJson("/urls/{$url->id}", [
            'original_url' => $expectedUrl
        ]);

        $response
            ->assertStatus(401)
            ->assertJson([
                'error' => 'You not is owner from this data'
            ]);
    }

    public function testShouldBeReturnErrorIfRegisterNotExist()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->putJson("/urls/99999", [
            'url_code' => 'code1234'
        ]);

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Data does not exist.'
            ]);
    }
}
