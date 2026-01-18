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
        $response->assertSee('商品の出品');
    }

    public function test_user_can_create_item()
    {
        Storage::fake('public');

        $user = User::factory()->create(['email_verified_at' => now()]);
        $category = Category::factory()->create(['content' => 'ファッション']);
        $condition = Condition::factory()->create(['content' => '新品、未使用']);
        $image = UploadedFile::fake()->create('item.jpeg', 100);

        $data = [
            'name'         => 'テスト商品',
            'description'  => 'テスト商品の説明です',
            'price'        => 1000,
            'categories'   => [$category->id],
            'condition_id' => $condition->id,
            'image'        => $image,
            'brand'        => 'ノーブランド',
        ];

        $response = $this->actingAs($user)->post('/sell', $data);
        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'name'        => 'テスト商品',
            'price'       => 1000,
            'user_id'     => $user->id,
            'brand_name'  => 'ノーブランド',
            'description' => 'テスト商品の説明です',
        ]);
    }

    public function test_sell_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/sell', []);
        $response->assertSessionHasErrors([
            'name',
            'description',
            'image',
            'categories',
            'condition_id',
            'price',
        ]);
    }

    public function test_sell_validation_price()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->post('/sell', ['price' => '千円']);
        $response->assertSessionHasErrors(['price']);

        $response2 = $this->actingAs($user)->post('/sell', ['price' => -100]);
        $response2->assertSessionHasErrors(['price']);
    }

    public function test_sell_validation_description_max_length()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $longDescription = str_repeat('a', 256);

        $response = $this->actingAs($user)->post('/sell', [
            'description' => $longDescription,
        ]);

        $response->assertSessionHasErrors(['description']);
    }

    public function test_sell_validation_image_extension()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Storage::fake('public');

        $text = UploadedFile::fake()->create('bad_file.txt', 100);

        $response = $this->actingAs($user)->post('/sell', [
            'image' => $text,
        ]);

        $response->assertSessionHasErrors(['image']);
    }
}
