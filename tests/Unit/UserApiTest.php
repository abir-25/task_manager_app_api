<?php

namespace Tests\Unit; // Change to Feature test namespace

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_user()
    {
        $response = $this->postJson('/api/v1/signup', [
            'username' => 'john@example.com',
            'password' => '123456Tt'
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id', 'username', 'password', 'created_at'
            ]);
    }

//    public function test_can_get_user_list()
//    {
//        User::factory()->count(3)->create();
//
//        $response = $this->getJson('/api/v1/users');
//
//        $response->assertStatus(200)
//            ->assertJsonCount(3);
//    }
}

