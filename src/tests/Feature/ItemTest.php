<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Like;
use App\Models\Category;
use App\Models\Comment;
use App\Models\Condition;
use App\Models\Profile;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 【ID:4】商品一覧取得：全商品が表示されるか
     */
    public function test_can_see_item_list()
    {
        $items = Item::factory()->count(3)->create();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($items[0]->name);
        $response->assertSee($items[1]->name);
    }

    /**
     * 【ID:4】商品一覧：購入済み商品は「Sold」と表示されるか
     */
    public function test_sold_item_shows_sold_label()
    {
        $item = Item::factory()->create();
        Purchase::factory()->create(['item_id' => $item->id]);
        $response = $this->get('/');

        // ▼▼▼ 修正：HTMLに合わせて大文字の SOLD にしたよ！ ▼▼▼
        $response->assertSee('SOLD');
    }

    /**
     * 【ID:4】商品一覧：自分が出品した商品は表示されない
     */
    public function test_does_not_show_own_items()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->create(['user_id' => $user->id, 'name' => '俺の商品']);
        $otherItem = Item::factory()->create(['name' => '他人の商品']);

        $response = $this->actingAs($user)->get('/');
        $response->assertSee('他人の商品');
        $response->assertDontSee('俺の商品');
    }

    /**
     * 【ID:5】マイリスト：いいねした商品だけが表示される
     */
    public function test_can_see_only_liked_items_in_mylist()
    {
        $user = User::factory()->create();
        $likedItem = Item::factory()->create(['name' => '好きな商品']);
        $otherItem = Item::factory()->create(['name' => '興味ない商品']);

        Like::factory()->create(['user_id' => $user->id, 'item_id' => $likedItem->id]);

        $response = $this->actingAs($user)->get('/?tab=mylist');
        $response->assertStatus(200);
        $response->assertSee('好きな商品');
        $response->assertDontSee('興味ない商品');
    }

    /**
     * 【ID:5】マイリスト：未認証（ゲスト）の場合は何も表示されない
     */
    public function test_guest_cannot_see_mylist()
    {
        $response = $this->get('/?tab=mylist');
        $response->assertDontSee('好きな商品');
    }

    /**
     * 【ID:6】商品検索：部分一致検索 & マイリストでの保持
     */
    public function test_search_items()
    {
        Item::factory()->create(['name' => '素敵な腕時計']);
        Item::factory()->create(['name' => '安いTシャツ']);

        // ▼▼▼ 修正：パラメータ名を search から keyword に変更！ ▼▼▼
        // 1. 普通に検索
        $response = $this->get('/?keyword=時計');

        $response->assertSee('素敵な腕時計');
        $response->assertDontSee('安いTシャツ');

        // 2. キーワードが保持されているかチェック
        $response->assertSee('value="時計"', false);

        // 3. マイリストページでも検索できるか
        $user = User::factory()->create();
        $myLikedItem = Item::factory()->create(['name' => '高級な腕時計']);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $myLikedItem->id]);

        // ▼▼▼ 修正：ここも search から keyword に変更！ ▼▼▼
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=時計');

        $response->assertSee('高級な腕時計');
        $response->assertSee('value="時計"', false);
    }

    /**
     * 【ID:7】商品詳細：必要な情報が表示される
     */
    public function test_can_see_item_detail()
    {
        $condition = Condition::factory()->create(['content' => '美品']);
        $item = Item::factory()->create([
            'name' => '高級時計',
            'brand_name' => 'ROLEX',
            'price' => 50000,
            'description' => 'すごい時計です',
            'img_url' => 'item_images/test.jpg',
            'condition_id' => $condition->id,
        ]);

        $cat1 = Category::factory()->create(['content' => 'ファッション']);
        $cat2 = Category::factory()->create(['content' => 'メンズ']);
        $item->categories()->attach([$cat1->id, $cat2->id]);

        $commentUser = User::factory()->create(['name' => '田中太郎']);
        Profile::factory()->create(['user_id' => $commentUser->id]);

        Comment::factory()->create([
            'item_id' => $item->id,
            'user_id' => $commentUser->id,
            'content' => 'コメントテスト'
        ]);

        Like::factory()->create(['item_id' => $item->id]);

        $response = $this->get("/item/{$item->id}");
        $response->assertStatus(200);

        $response->assertSee('高級時計');
        $response->assertSee('ROLEX');
        $response->assertSee('50,000');
        $response->assertSee('すごい時計です');
        $response->assertSee('美品');
        $response->assertSee('1');
        $response->assertSee('コメントテスト');
        $response->assertSee('ファッション');
        $response->assertSee('メンズ');
        $response->assertSee('item_images/test.jpg');
        $response->assertSee('田中太郎');
    }

    /**
     * 【ID:8】いいね機能：登録と解除、アイコン変化(active)、数増加
     */
    public function test_user_can_like_and_unlike()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 1. ログインして画面を見る（初期状態）
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('0');
        $response->assertDontSee('active');

        // 2. いいねする
        $this->actingAs($user)->post("/item/{$item->id}/like");
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'item_id' => $item->id]);

        // 3. 画面変化確認
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('1');
        $response->assertSee('active');

        // 4. 解除する
        $this->actingAs($user)->post("/item/{$item->id}/like");
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'item_id' => $item->id]);

        // 5. 画面変化確認
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('0');
        $response->assertDontSee('active');
    }

    /**
     * 【ID:9】コメント送信機能
     */
    public function test_user_can_comment()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '素敵な商品ですね！',
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('comments', [
            'item_id' => $item->id,
            'user_id' => $user->id,
            'content' => '素敵な商品ですね！',
        ]);

        $response = $this->get("/item/{$item->id}");
        $response->assertSee('1');
        $response->assertSee('素敵な商品ですね！');
    }

    /**
     * 【ID:9】コメント送信：ゲストは送信できない
     */
    public function test_guest_cannot_comment()
    {
        $item = Item::factory()->create();

        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストのコメント',
        ]);

        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', ['content' => 'ゲストのコメント']);
    }

    /**
     * 【ID:9】コメント：バリデーション（空文字）
     */
    public function test_comment_validation_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '',
        ]);

        $response->assertSessionHasErrors(['comment']);
    }

    /**
     * 【ID:9】コメント：バリデーション（255文字以上）
     */
    public function test_comment_validation_length()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        $longComment = str_repeat('a', 256);

        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => $longComment,
        ]);

        $response->assertSessionHasErrors(['comment']);
    }
}
