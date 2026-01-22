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

    public function test_can_see_item_list()
    {
        $items = Item::factory()->count(3)->create();
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee($items[0]->name);
        $response->assertSee($items[1]->name);
    }

    public function test_sold_item_shows_sold_label()
    {
        $item = Item::factory()->create();
        Purchase::factory()->create(['item_id' => $item->id]);
        $response = $this->get('/');
        $response->assertSee('SOLD');
    }

    public function test_does_not_show_own_items()
    {
        $user = User::factory()->create();
        $myItem = Item::factory()->create(['user_id' => $user->id, 'name' => '俺の商品']);
        $otherItem = Item::factory()->create(['name' => '他人の商品']);
        $response = $this->actingAs($user)->get('/');
        $response->assertSee('他人の商品');
        $response->assertDontSee('俺の商品');
    }

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

    public function test_guest_cannot_see_mylist()
    {
        $response = $this->get('/?tab=mylist');
        $response->assertDontSee('好きな商品');
    }

    public function test_search_items()
    {
        Item::factory()->create(['name' => '素敵な腕時計']);
        Item::factory()->create(['name' => '安いTシャツ']);
        $response = $this->get('/?keyword=時計');
        $response->assertSee('素敵な腕時計');
        $response->assertDontSee('安いTシャツ');
        $response->assertSee('value="時計"', false);
        $user = User::factory()->create();
        $myLikedItem = Item::factory()->create(['name' => '高級な腕時計']);
        Like::factory()->create(['user_id' => $user->id, 'item_id' => $myLikedItem->id]);
        $response = $this->actingAs($user)->get('/?tab=mylist&keyword=時計');
        $response->assertSee('高級な腕時計');
        $response->assertSee('value="時計"', false);
    }

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
            'content' => 'コメントテスト',
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

    public function test_user_can_like_and_unlike()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('0');
        $response->assertDontSee('active');
        $this->actingAs($user)->post("/item/{$item->id}/like");
        $this->assertDatabaseHas('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('1');
        $response->assertSee('active');
        $this->actingAs($user)->post("/item/{$item->id}/like");
        $this->assertDatabaseMissing('likes', ['user_id' => $user->id, 'item_id' => $item->id]);
        $response = $this->actingAs($user)->get("/item/{$item->id}");
        $response->assertSee('0');
        $response->assertDontSee('active');
    }

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

    public function test_guest_cannot_comment()
    {
        $item = Item::factory()->create();
        $response = $this->post("/item/{$item->id}/comment", [
            'comment' => 'ゲストのコメント',
        ]);
        $response->assertRedirect('/login');
        $this->assertDatabaseMissing('comments', ['content' => 'ゲストのコメント']);
    }

    public function test_comment_validation_required()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();
        $response = $this->actingAs($user)->post("/item/{$item->id}/comment", [
            'comment' => '',
        ]);
        $response->assertSessionHasErrors(['comment']);
    }

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