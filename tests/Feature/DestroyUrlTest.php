<?php

namespace Tests\Feature;

use App\Url;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DestroyUrlTest extends TestCase
{
    use RefreshDatabase;

    public function testShouldBeReturnErrorIfRegisterNotExist()
    {
        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->deleteJson("/urls/99999");

        $response
            ->assertStatus(400)
            ->assertJson([
                'error' => 'Data does not exist.'
            ]);
    }

    public function testNotBeAbleDestroyUrlIfUserNotOwner()
    {
        $url = factory(Url::class)->make();
        $url->save();

        $response = $this->withHeaders([
            'User-Header' => 'Maria.Eduarda',
        ])->deleteJson("/urls/{$url->id}");

        $response
            ->assertStatus(401)
            ->assertJson([
                'error' => 'You not is owner from this data'
            ]);
    }

    public function testShouldErrorIfNotHeaderUser()
    {
        $expectedUrl = 'https://rhizom.me/';
        $expectedCode = 'code123';

        $response = $this->deleteJson("/urls/{$expectedCode}", [
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

        $response = $this->withHeaders([
            'User-Header' => $url->user_name,
        ])->deleteJson("/urls/{$url->id}");

        $response
            ->assertStatus(204);

        $isDeleted = Url::find($url->id);
        $this->assertNull($isDeleted);
    }
}
