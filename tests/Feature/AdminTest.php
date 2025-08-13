<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_approve_user()
    {
        $admin = User::factory()->admin()->create();
        $user = User::factory()->inactive()->create();

        $token = $admin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/users/{$user->id}/approve");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'کاربر تایید و پیامک ارسال شد.']);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_approve_user()
    {
        $user = User::factory()->create();
        $targetUser = User::factory()->inactive()->create();

        $token = $user->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/users/{$targetUser->id}/approve");

        $response->assertStatus(403)
                 ->assertJson(['message' => 'دسترسی غیرمجاز']);
    }
}
