<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification; // 通知（メール）のテストに使う
use Illuminate\Auth\Notifications\VerifyEmail; // メール認証の通知クラス
use Illuminate\Support\Facades\URL; // URL生成に使う

class LoginTest extends TestCase
{
    use RefreshDatabase; // テストのたびにデータベースをリセットしてキレイにする

    /**
     * 【ID:1】会員登録：必須項目（名前）が未入力の場合、エラー
     */
    public function test_register_name_required_error()
    {
        $response = $this->post('/register', [
            'name' => '', // 空っぽ
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        // 「お名前を入力してください」などのエラーが出ているか確認
        $response->assertSessionHasErrors(['name']);
    }

    /**
     * 【ID:1】会員登録：必須項目（メール）が未入力の場合、エラー
     */
    public function test_register_email_required_error()
    {
        $response = $this->post('/register', [
            'name' => 'TestUser',
            'email' => '', // 空っぽ
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * 【ID:1】会員登録：必須項目（パスワード）が未入力の場合、エラー
     */
    public function test_register_password_required_error()
    {
        $response = $this->post('/register', [
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => '', // 空っぽ
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 【ID:1】会員登録：パスワードが7文字以下の場合、エラー
     */
    public function test_register_password_length_error()
    {
        $response = $this->post('/register', [
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => '1234567', // 7文字（短すぎる）
            'password_confirmation' => '1234567',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 【ID:1】会員登録：パスワードと確認用パスワードが不一致の場合、エラー
     */
    public function test_register_password_mismatch_error()
    {
        $response = $this->post('/register', [
            'name' => 'TestUser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password999', // 違うパスワード
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 【ID:1, 16】会員登録成功 → メール認証画面へ遷移 → メールが送られているか
     */
    public function test_user_can_register_and_email_is_sent()
    {
        // 実際のメールは送らず、「送ったフリ」をするモードにする
        Notification::fake();

        // 1. 会員登録を実行
        $response = $this->post('/register', [
            'name' => 'New User',
            'email' => 'new@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        // 2. 登録後は「メール認証確認画面」へリダイレクトされること
        $response->assertRedirect('/email/verify');

        // 3. データベースにユーザーが作成されたか確認
        $user = User::where('email', 'new@example.com')->first();
        $this->assertNotNull($user);

        // 4. そのユーザーに「メール認証の通知」が送られたことを確認
        // （これが「会員登録後、認証メールが送信される」のテスト！）
        Notification::assertSentTo(
            [$user],
            VerifyEmail::class
        );
    }

    /**
     * 【ID:16】メール認証誘導画面の確認
     * 「認証はこちらから」ボタンが表示され、正しいリンク（MailHog）になっているか
     */
    public function test_email_verification_screen_has_button()
    {
        // 1. まだ認証していないユーザーを作る
        $user = User::factory()->unverified()->create();

        // 2. そのユーザーでログインして、「メール認証画面」を開く
        $response = $this->actingAs($user)->get('/email/verify');

        // 3. 画面が正しく表示されること (200 OK)
        $response->assertStatus(200);

        // 4. 「認証はこちらから」という文字（ボタン）があるか確認
        $response->assertSee('認証はこちらから');

        // 5. そのボタンのリンク先が、メール確認ツール（http://localhost:8025）になっているか確認
        // ※bladeファイルに書かれている通り、これがあれば合格！
        $response->assertSee('http://localhost:8025');
    }

    /**
     * 【ID:16】メール認証完了後 → プロフィール設定画面へ遷移
     */
    public function test_user_can_verify_email_and_redirect_to_profile_setup()
    {
        // 1. 未認証のユーザーを作る
        $user = User::factory()->unverified()->create();

        // 2. 正しい「認証用URL」を人工的に作る（メールの中のリンクを再現）
        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        // 3. そのURLにアクセスする（メールのリンクをクリックした動き）
        $response = $this->actingAs($user)->get($verificationUrl);

        // 4. 認証成功後は「プロフィール設定画面」へリダイレクトされるはず！
        $response->assertRedirect('/mypage/profile');

        // 5. データベース上で「認証済み（日付が入っている）」になっているか確認
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    /**
     * 【ID:2】ログイン失敗：メール未入力
     */
    public function test_login_failed_email_required()
    {
        $response = $this->post('/login', [
            'email' => '',
            'password' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);
    }

    /**
     * 【ID:2】ログイン失敗：パスワード未入力
     */
    public function test_login_failed_password_required()
    {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => '',
        ]);
        $response->assertSessionHasErrors(['password']);
    }

    /**
     * 【ID:2】ログイン失敗：情報間違い
     */
    public function test_login_failed_wrong_credentials()
    {
        // 正しいユーザーを用意
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        // 間違ったパスワードで送信
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        // 元の画面（ログイン画面）にリダイレクトされる
        $response->assertStatus(302);

        // エラーメッセージ（ログイン情報が登録されていません 等）が出ているか
        $response->assertSessionHasErrors();

        // まだゲスト（未ログイン）であることを確認
        $this->assertGuest();
    }

    /**
     * 【ID:2】ログイン成功
     */
    public function test_user_can_login()
    {
        // ユーザーを用意（今回はメール認証不要なログインテストとして実行、または認証済みとする）
        $user = User::factory()->create([
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        // ログイン後はトップページ（/）へリダイレクト
        $response->assertRedirect('/');

        // システム上、「このユーザーでログイン中」になっているか
        $this->assertAuthenticatedAs($user);
    }

    /**
     * 【ID:3】ログアウト成功
     */
    public function test_user_can_logout()
    {
        $user = User::factory()->create();

        // ログイン状態でログアウトボタンを押す
        $response = $this->actingAs($user)->post('/logout');

        // ログアウト後はログイン画面（/login）へリダイレクト
        $response->assertRedirect('/login');

        // ゲストに戻ったか確認
        $this->assertGuest();
    }
}
