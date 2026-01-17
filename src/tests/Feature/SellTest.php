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

    /**
     * 【画面アクセス】未ログインユーザーは出品画面にアクセスできない
     */
    public function test_guest_cannot_access_sell_page()
    {
        $response = $this->get('/sell');
        $response->assertRedirect('/login');
    }

    /**
     * 【画面アクセス】ログインユーザーは出品画面にアクセスできる
     */
    public function test_user_can_access_sell_page()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        $response = $this->actingAs($user)->get('/sell');
        $response->assertStatus(200);
        $response->assertSee('商品の出品');
    }

    /**
     * 【出品機能】正しく情報を入力すれば商品を出品できる
     */
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

        // ★修正①：トップページへのリダイレクトを厳密にチェック！
        $response->assertRedirect('/');

        $this->assertDatabaseHas('items', [
            'name'        => 'テスト商品',
            'price'       => 1000,
            'user_id'     => $user->id,
            'brand_name'  => 'ノーブランド',
            'description' => 'テスト商品の説明です',
        ]);
    }

    /**
     * 【バリデーション】必須項目が空だと出品できない
     */
    public function test_sell_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // ★修正②：空っぽの配列を送って、全項目の必須チェックを確認！
        $response = $this->actingAs($user)->post('/sell', []);

        $response->assertSessionHasErrors([
            'name',         // 商品名
            'description',  // 商品説明
            'image',        // 商品画像
            'categories',   // 商品のカテゴリー
            'condition_id', // 商品の状態
            'price',        // 商品価格
        ]);
    }

    /**
     * 【バリデーション】価格は数値で0円以上でないといけない
     */
    public function test_sell_validation_price()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // 数値じゃない
        $response = $this->actingAs($user)->post('/sell', ['price' => '千円']);
        $response->assertSessionHasErrors(['price']);

        // マイナス
        $response2 = $this->actingAs($user)->post('/sell', ['price' => -100]);
        $response2->assertSessionHasErrors(['price']);
    }

    /**
     * ★追加③：【バリデーション】商品説明は255文字以内でないとNG
     */
    public function test_sell_validation_description_max_length()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);

        // 256文字の文章を作る（ 'a' を256回繰り返す）
        $longDescription = str_repeat('a', 256);

        $response = $this->actingAs($user)->post('/sell', [
            'description' => $longDescription,
        ]);

        $response->assertSessionHasErrors(['description']);
    }

    /**
     * ★追加④：【バリデーション】画像の拡張子は .jpeg か .png 以外NG
     */
    public function test_sell_validation_image_extension()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Storage::fake('public');

        // テキストファイル (.txt) を送ってみる
        $text = UploadedFile::fake()->create('bad_file.txt', 100);

        $response = $this->actingAs($user)->post('/sell', [
            'image' => $text,
        ]);

        $response->assertSessionHasErrors(['image']);
    }
}
