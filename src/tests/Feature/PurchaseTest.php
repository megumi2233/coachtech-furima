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
        $response->assertSee('テスト住所');
        $response->assertSee('123-4567');
    }

    public function test_purchase_validation_payment_method_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);
        
        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => '', 
        ]);
        
        $response->assertSessionHasErrors(['payment_method' => '支払い方法を選択してください']);
    }

    public function test_payment_method_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get("/purchase/{$item->id}?payment_method=konbini");
        
        $response->assertStatus(200);
        $response->assertSee('コンビニ支払い');
    }

    public function test_updated_address_is_reflected_on_purchase_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create();
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '000-0000',
            'address' => '古い住所',
        ]);
        
        $newData = [
            'postal_code' => '999-9999',
            'address' => '新しい住所',
            'building' => '新しいビル',
        ];
        
        $response = $this->actingAs($user)->post(route('purchase.address.update', ['item_id' => $item->id]), $newData);
        
        $response->assertRedirect("/purchase/{$item->id}");
        
        $responsePurchasePage = $this->actingAs($user)->get("/purchase/{$item->id}");
        
        $responsePurchasePage->assertSee('新しい住所');
        $responsePurchasePage->assertSee('999-9999');
        $responsePurchasePage->assertDontSee('古い住所');
    }

    public function test_purchase_success_and_link_to_user()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $item = Item::factory()->create(['name' => 'テスト商品A']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '111-1111',
            'address' => '東京都渋谷区',
        ]);

        $response = $this->actingAs($user)->post("/purchase/{$item->id}", [
            'payment_method' => 'konbini',
        ]);
        
        $actualRedirectUrl = $response->headers->get('Location');
        $this->assertStringContainsString('checkout.stripe.com', $actualRedirectUrl);

        $responseSuccess = $this->actingAs($user)->get(route('purchase.success', ['item_id' => $item->id]) . '?payment_method=konbini');
        
        $responseSuccess->assertRedirect('/');
        
        $this->assertDatabaseHas('purchases', [
            'item_id' => $item->id,
            'user_id' => $user->id,
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
        
        $response->assertSessionHasErrors([
            'postal_code', 
            'address' => '住所を入力してください',
        ]);
    }
}
