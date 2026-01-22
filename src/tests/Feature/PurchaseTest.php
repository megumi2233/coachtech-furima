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

    public function test_guest_cannot_access_purchase_page()
    {
        $item = Item::factory()->create();
        $response = $this->get("/purchase/{$item->id}");
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_purchase_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '123-4567',
            'address' => 'テスト住所',
        ]);
        $response = $this->actingAs($user)->get("/purchase/{$item->id}");
        $response->assertStatus(200);
        $response->assertSee('支払い方法');
    }

    public function test_purchase_validation_payment_method_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => '',
        ]);
        $response->assertSessionHasErrors(['payment_method']);
    }

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
        $response = $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]) . '?payment_method=konbini');
        $response->assertRedirect('/');
        $this->assertDatabaseHas('purchases', [
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => 'konbini',
        ]);
        $responseIndex = $this->actingAs($user)->get('/');
        $responseIndex->assertSee('SOLD');
        $responseMyPage = $this->actingAs($user)->get('/mypage?page=buy');
        $responseMyPage->assertSee('テスト商品A');
    }

    public function test_address_update_validation()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), [
            'postal_code' => '1234567',
            'address' => '',
        ]);
        $response->assertSessionHasErrors(['postal_code', 'address']);
    }

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
        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), $newData);
        $response->assertRedirect("/purchase/{$item->id}");
        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'zipcode' => '999-9999',
            'address' => '新しい住所',
        ]);
    }
}