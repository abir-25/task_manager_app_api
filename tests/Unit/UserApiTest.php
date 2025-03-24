<?php

namespace Tests\Unit;
use Tests\TestCase;

class UserApiTest extends TestCase
{
    protected $token;

    public function test_can_create_user()
    {
        $response = $this->postJson('/api/v1/signup', [
            'username' => 'abir25@gmail.com',
            'password' => '123456Tt'
        ]);
        $this->token = $response->json('data')["jwToken"];
        $this->assertNotEmpty($this->token, "JWT Token is missing in the response!");
    }
}

