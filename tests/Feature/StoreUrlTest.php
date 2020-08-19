<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StoreUrlTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->postJson('/urls', ['url' => 'https://rhizom.me/']);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'original_url',
                'short_url'
            ]);
    }
}
