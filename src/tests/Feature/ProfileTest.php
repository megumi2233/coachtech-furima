<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Profile;
use App\Models\Item;
use App\Models\Purchase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 【MyPage】未ログインユーザーはマイページにアクセスできない
     */
    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get('/mypage');
        $response->assertRedirect('/login');
    }

    /**
     * 【MyPage】マイページに必要な情報（プロフィール・出品・購入商品）が表示される
     */
    public function test_user_can_see_mypage_info()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'name' => 'テスト太郎']);

        Profile::factory()->create([
            'user_id' => $user->id,
            'avatar_url' => 'profile_images/dummy.jpg'
        ]);

        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分が出品した服'
        ]);

        $otherItem = Item::factory()->create(['name' => '他人が出品した本']);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $otherItem->id
        ]);

        // 1. 初期表示（出品タブ）の確認
        // ※Bladeの仕様通り、初期状態では「出品した商品」だけが見えるはず
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);

        $response->assertSee('テスト太郎');
        $response->assertSee('自分が出品した服');
        $response->assertSee('dummy.jpg');

        // 2. タブを切り替えて「購入した商品」を確認
        // ※Bladeの仕様通り、?page=buy をつけると表示されるはず
        $responseBuy = $this->actingAs($user)->get('/mypage?page=buy');
        $responseBuy->assertSee('他人が出品した本');
    }

    /**
     * 【Profile】プロフィール設定画面で初期値が表示されている
     */
    public function test_user_can_see_edit_profile_screen_with_initial_values()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'name' => '初期値確認太郎']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '111-2222',
            'address' => '東京都千代田区',
            'building_name' => 'テストビル101',
            'avatar_url' => 'initial.jpg'
        ]);

        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);

        $response->assertSee('value="初期値確認太郎"', false);
        $response->assertSee('value="111-2222"', false);
        $response->assertSee('value="東京都千代田区"', false);
        $response->assertSee('value="テストビル101"', false);
    }

    /**
     * 【Profile】プロフィールを正常に更新できる
     */
    public function test_user_can_update_profile()
    {
        Storage::fake('public');

        $user = User::factory()->create(['email_verified_at' => now()]);
        Profile::factory()->create(['user_id' => $user->id]);

        // ▼ めぐみさんのアイデア採用！GDを使わずに .jpeg のダミーファイルを作成
        $avatar = UploadedFile::fake()->create('avatar.jpeg', 100);

        // Bladeの name属性 に合わせてデータを送信
        $updateData = [
            'name'          => '変更後の名前',
            'postal_code'   => '999-9999',      // blade: name="postal_code"
            'address'       => '大阪府大阪市',    // blade: name="address"
            'building'      => '更新ビル',        // blade: name="building"
            'profile_image' => $avatar,           // blade: name="profile_image"
        ];

        $response = $this->actingAs($user)->post('/mypage/profile', $updateData);

        // コントローラーの修正に合わせてマイページへリダイレクト
        $response->assertRedirect('/mypage');

        $this->assertDatabaseHas('users', ['name' => '変更後の名前']);
        $this->assertDatabaseHas('profiles', [
            'zipcode'       => '999-9999',
            'address'       => '大阪府大阪市',
            'building_name' => '更新ビル',
        ]);
    }

    /**
     * 【Validation】郵便番号にハイフンがないとエラー
     */
    public function test_profile_update_validation_zipcode()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name'        => 'テストユーザー',
            'postal_code' => '1234567', // ハイフンなしNG
            'address'     => '住所',
        ]);

        $response->assertSessionHasErrors(['postal_code']);
    }

    /**
     * 【Validation】必須項目チェック
     */
    public function test_profile_update_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name'        => '',
            'postal_code' => '',
            'address'     => '',
        ]);

        $response->assertSessionHasErrors(['name', 'postal_code', 'address']);
    }

    /**
     * 【Validation】画像の拡張子が不適切だとエラー
     */
    public function test_profile_update_validation_image_extension()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Storage::fake('public');

        // テキストファイルは画像じゃないのでエラーになるはず
        $text = UploadedFile::fake()->create('document.txt', 100);

        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name'          => '正しい名前',
            'postal_code'   => '123-4567',
            'address'       => '住所',
            'profile_image' => $text,
        ]);

        $response->assertSessionHasErrors(['profile_image']);
    }
}
