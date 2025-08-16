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
        // یک ادمین و یک کاربر غیرفعال درست می‌کنیم
        $admin = User::factory()->admin()->create();
        $user = User::factory()->inactive()->create();

        // توکن ادمین
        $token = $admin->createToken('auth_token')->plainTextToken;

        // مسیر جدید: /api/admin/users/{id}/approve
        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/admin/users/{$user->id}/approve");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'کاربر تایید و پیامک ارسال شد.']);

        // بررسی شد که کاربر فعال شده و approved_by برابر id ادمین است
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'active' => true,
            'approved_by' => $admin->id,
        ]);
    }

    public function test_non_admin_cannot_approve_user()
    {
        // یک کاربر عادی (غیر ادمین) و یک کاربر هدف غیرفعال
        $nonAdmin = User::factory()->create();
        $targetUser = User::factory()->inactive()->create();

        $token = $nonAdmin->createToken('auth_token')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => "Bearer $token",
        ])->patchJson("/api/admin/users/{$targetUser->id}/approve");

        $response->assertStatus(403)
                 ->assertJson(['message' => 'دسترسی غیرمجاز']);

        // مطمئن می‌شویم وضعیت کاربر هدف همچنان غیرفعال است
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'active' => false,
        ]);
    }
}
