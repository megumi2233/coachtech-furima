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

    public function test_guest_cannot_access_mypage()
    {
        $response = $this->get('/mypage');
        $response->assertRedirect('/login');
    }

    public function test_user_can_see_mypage_info()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'name' => 'テスト太郎']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'avatar_url' => 'profile_images/dummy.jpg',
        ]);
        
        Item::factory()->create([
            'user_id' => $user->id,
            'name' => '自分が出品した服',
        ]);
        
        $otherItem = Item::factory()->create(['name' => '他人が出品した本']);
        Purchase::factory()->create([
            'user_id' => $user->id,
            'item_id' => $otherItem->id,
        ]);
        
        $response = $this->actingAs($user)->get('/mypage');
        $response->assertStatus(200);
        $response->assertSee('テスト太郎');
        $response->assertSee('自分が出品した服');
        $response->assertSee('dummy.jpg');
        
        $responseBuy = $this->actingAs($user)->get('/mypage?page=buy');
        $responseBuy->assertSee('他人が出品した本');
    }

    public function test_user_can_see_edit_profile_screen_with_initial_values()
    {
        $user = User::factory()->create(['email_verified_at' => now(), 'name' => '初期値確認太郎']);
        Profile::factory()->create([
            'user_id' => $user->id,
            'zipcode' => '111-2222',
            'address' => '東京都千代田区',
            'building_name' => 'テストビル101',
            'avatar_url' => 'initial.jpg',
        ]);
        
        $response = $this->actingAs($user)->get('/mypage/profile');
        $response->assertStatus(200);
        
        $response->assertSee('value="初期値確認太郎"', false);
        $response->assertSee('value="111-2222"', false);
        $response->assertSee('value="東京都千代田区"', false);
        $response->assertSee('value="テストビル101"', false);
        $response->assertSee('initial.jpg');
    }

    public function test_user_can_update_profile()
    {
        Storage::fake('public');
        $user = User::factory()->create(['email_verified_at' => now()]);
        Profile::factory()->create(['user_id' => $user->id]);
        
        $avatar = UploadedFile::fake()->create('avatar.jpeg', 100);
        
        $updateData = [
            'name' => '変更後の名前',
            'postal_code' => '999-9999',
            'address' => '大阪府大阪市',
            'building' => '更新ビル',
            'profile_image' => $avatar,
        ];
        
        $response = $this->actingAs($user)
            ->from('/mypage/profile')
            ->post('/mypage/profile', $updateData);
            
        $response->assertRedirect('/mypage');
        
        $this->assertDatabaseHas('users', ['name' => '変更後の名前']);
        $this->assertDatabaseHas('profiles', [
            'zipcode' => '999-9999',
            'address' => '大阪府大阪市',
            'building_name' => '更新ビル',
        ]);
    }

    public function test_profile_update_validation_zipcode()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => 'テストユーザー',
            'postal_code' => '1234567',
            'address' => '住所',
        ]);
        
        $response->assertSessionHasErrors(['postal_code' => '郵便番号はハイフンありの8文字で入力してください']);
    }

    public function test_profile_update_validation_required()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => '',
            'postal_code' => '',
            'address' => '',
        ]);
        
        $response->assertSessionHasErrors([
            'name' => 'ユーザー名を入力してください',
            'postal_code' => '郵便番号を入力してください',
            'address' => '住所を入力してください',
        ]);
    }

    public function test_profile_update_validation_image_extension()
    {
        $user = User::factory()->create(['email_verified_at' => now()]);
        Storage::fake('public');
        
        $text = UploadedFile::fake()->create('document.txt', 100);
        
        $response = $this->actingAs($user)->post('/mypage/profile', [
            'name' => '正しい名前',
            'postal_code' => '123-4567',
            'address' => '住所',
            'profile_image' => $text,
        ]);
        
        $response->assertSessionHasErrors(['profile_image' => '画像はjpegまたはpng形式でアップロードしてください']);
    }
}
