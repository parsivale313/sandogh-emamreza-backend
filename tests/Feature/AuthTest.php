<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register()
    {
        $response = $this->postJson('/api/register', [
            'first_name' => 'Ali',
            'last_name' => 'Ahmadi',
            'phone' => '09123456789',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJson(['message' => 'ثبت‌نام انجام شد. منتظر تایید ادمین باشید.']);

        $this->assertDatabaseHas('users', [
            'phone' => '09123456789',
            'first_name' => 'Ali',
        ]);
    }

    public function test_inactive_user_cannot_login()
    {
        $user = User::factory()->inactive()->create([
            'phone' => '09111111111',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'phone' => '09111111111',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
    }

    public function test_active_user_can_login()
    {
        $user = User::factory()->create([
            'phone' => '09122222222',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/login', [
            'phone' => '09122222222',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $token = $user->createToken('test-token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token"
        ])->postJson('/api/logout');

        $response->assertStatus(200);
        $this->assertCount(0, $user->tokens()->get());
    }
}
