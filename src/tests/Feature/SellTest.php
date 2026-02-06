<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Category;
use App\Models\Condition;

class SellTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_sell_page()
    {
        $response = $this->get('/sell');
        $response->assertRedirect('/login');
    }

    public function test_user_can_access_sell_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);
    }

    public function test_user_can_create_item()
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::factory()->create(['content' => 'ファッション']);
        $condition = Condition::factory()->create(['content' => '新品、未使用']);
        $image = UploadedFile::fake()->create('item.jpeg', 100);
        
        $data = [
            'name' => 'テスト商品',
            'description' => 'テスト商品の説明です',
            'price' => 1000,
            'categories' => [$category->id],
            'condition_id' => $condition->id,
            'image' => $image,
            'brand' => 'ノーブランド',
        ];
        
        $response = $this->actingAs($user)->post('/sell', $data);
        
        $response->assertRedirect('/');
        
        $this->assertDatabaseHas('items', [
            'name' => 'テスト商品',
            'price' => 1000,
            'user_id' => $user->id,
            'brand_name' => 'ノーブランド',
            'description' => 'テスト商品の説明です',
            'condition_id' => $condition->id,
        ]);
    }

    public function test_sell_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/sell', []);
        
        $response->assertSessionHasErrors([
            'image' => '商品画像を選択してください',
            'categories' => 'カテゴリーを選択してください',
            'condition_id' => '商品の状態を選択してください',
            'name' => '商品名を入力してください',
            'description' => '商品説明を入力してください',
            'price' => '販売価格を入力してください',
        ]);
    }

    public function test_sell_validation_price_numeric()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/sell', ['price' => '千円']);
        
        $response->assertSessionHasErrors(['price' => '販売価格は数値で入力してください']);
    }

    public function test_sell_validation_price_min()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/sell', ['price' => -100]);
        
        $response->assertSessionHasErrors(['price' => '販売価格は0円以上で入力してください']);
    }

    public function test_sell_validation_description_max_length()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $longDescription = str_repeat('a', 256);
        
        $response = $this->actingAs($user)->post('/sell', [
            'description' => $longDescription,
        ]);
        
        $response->assertSessionHasErrors(['description' => '商品説明は255文字以内で入力してください']);
    }

    public function test_sell_validation_image_extension()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Storage::fake('public');
        $text = UploadedFile::fake()->create('bad_file.txt', 100);
        
        $response = $this->actingAs($user)->post('/sell', [
            'image' => $text,
        ]);
        
        $response->assertSessionHasErrors(['image' => '画像はjpegまたはpng形式でアップロードしてください']);
    }
}
