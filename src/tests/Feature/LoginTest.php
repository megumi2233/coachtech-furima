<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_failed_email_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email' => 'メールアドレスを入力してください']);
    }

    public function test_login_failed_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password' => 'パスワードを入力してください']);
    }

    public function test_login_failed_wrong_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);
        $response->assertSessionHasErrors(['email' => 'ログイン情報が登録されていません']);
    }

    public function test_user_can_login()
    {
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);
        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login'); 
        $this->assertGuest();
    }
}
