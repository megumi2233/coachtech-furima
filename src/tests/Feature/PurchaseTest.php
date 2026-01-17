<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Profile;
use App\Models\Purchase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 【画面アクセス】未ログインユーザーは購入画面にアクセスできない
     */
    public function test_guest_cannot_access_purchase_page()
    {
        $item = Item::factory()->create();
        $response = $this->get("/purchase/{$item->id}");
        $response->assertRedirect('/login');
    }

    /**
     * 【画面アクセス】ログインユーザーは購入画面にアクセスできる
     */
    public function test_user_can_access_purchase_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();

        // 画面表示時に住所情報が必要なのでプロフィールを作成
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '123-4567',
            'address' => 'テスト住所',
        ]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
    }

    /**
     * 【バリデーション】支払い方法を選択しないとエラーになる
     */
    public function test_purchase_validation_payment_method_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        // わざと空っぽで送信
        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => '',
        ]);

        $response->assertSessionHasErrors(['payment_method']);
    }

    /**
     * 【購入成功】購入処理、SOLD表示、プロフィール追加をすべて確認！
     */
    public function test_purchase_success_flow()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['name' => 'テスト商品A']);

        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '111-1111',
            'address' => '東京都渋谷区',
            'building_name' => 'テストビル',
        ]);

        // 1. Stripe決済成功後のURLにアクセス（擬似的に成功させる）
        $response = $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]) . '?payment_method=konbini');

        // 2. トップページへリダイレクトされるか確認
        $response->assertRedirect('/');

        // 3. データベースに購入履歴があるか確認
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'konbini',
        ]);

        // 4. トップページで「SOLD」と表示されているか確認！
        // （index.blade.phpの実装に合わせてチェック有効化！）
        $responseIndex = $this->actingAs($user)->get('/');
        $responseIndex->assertSee('SOLD');

        // 5. 「プロフィール/購入した商品一覧」に追加されているか確認！
        $responseMyPage = $this->actingAs($user)->get('/mypage?page=buy');
        $responseMyPage->assertSee('テスト商品A');
    }

    /**
     * 【住所変更】バリデーション（郵便番号など）
     */
    public function test_address_update_validation()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '1234567', // ハイフンなしはNG
            'address' => '', // 必須NG
        ]);

        $response->assertSessionHasErrors(['postal_code', 'address']);
    }

    /**
     * 【住所変更】成功パターン
     */
    public function test_user_can_update_shipping_address()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '000-0000',
        ]);

        $newData = [
            'postal_code' => '999-9999',
            'address' => '新しい住所',
            'building' => '新しいビル',
        ];

        // 住所変更処理を実行
        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), $newData);

        // ★修正：変更後は「購入画面」に戻ることを厳密に確認！
        $response->assertRedirect("/purchase/{$item->id}");

        // DBの更新確認
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'zipcode' => '999-9999',
            'address' => '新しい住所',
        ]);
    }
}
