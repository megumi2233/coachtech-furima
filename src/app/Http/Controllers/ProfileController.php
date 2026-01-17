<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProfileRequest; 
use App\Models\Profile; 
use App\Models\User;
use App\Http\Requests\AddressRequest;
use App\Models\Item;
use App\Models\Purchase;

class ProfileController extends Controller
{
// ★追加：マイページ（プロフィールTOP）を表示するメソッド
    public function index()
    {
        $user = Auth::user();
        
        // 1. ユーザー情報とプロフィール情報を取得
        $profile = Profile::where('user_id', $user->id)->first();

        // 2. この人が「購入した商品」のリストを取得
        // （purchasesテーブルから、item情報も一緒に持ってくる）
        $purchasedItems = Purchase::where('user_id', $user->id)->with('item')->get();

        // 3. この人が「出品した商品」のリストを取得
        $soldItems = Item::where('user_id', $user->id)->get();

        return view('mypage.profile', compact('user', 'profile', 'purchasedItems', 'soldItems'));
    }

    public function edit()
    {
        $user = Auth::user();
        // ユーザーに紐づくプロフィール情報を取得（なければnull）
        $profile = Profile::where('user_id', $user->id)->first();

        return view('mypage.edit', compact('user', 'profile'));
    }

    public function update(ProfileRequest $request)
    {
        $user = Auth::user();

        // 1. ユーザー情報（名前）の更新
        $user->name = $request->name;
        $user->save();

        // 2. プロフィール情報の準備
        // ★ここを変更！フォームの 'postal_code' を DBの 'zipcode' に入れる
        $profileData = [
            'zipcode'       => $request->postal_code, 
            'address'       => $request->address,   
            'building_name' => $request->building,  
        ];

        // 3. 画像がアップロードされていたら保存
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile_images', 'public');
            // DBのカラム名 'avatar_url' にパスを保存（テーブル仕様書通り！）
            $profileData['avatar_url'] = $path; 
        }

        // 4. プロフィールを更新（なければ作成）
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $profileData
        );

        return redirect('/mypage');
    }

    // ★配送先変更画面を表示する
    public function editAddress($item_id)
    {
        // どの商品の購入中に住所を変えたいのか、IDを受け取るよ
        return view('purchase.address', ['item_id' => $item_id]);
    }

    // ★住所を更新して、購入画面に戻る
    public function updateAddress(AddressRequest $request, $item_id)
    {
        $user = Auth::user();

        // プロフィール情報の「住所」の部分だけを書き換えるよ
        // （※今回はprofileテーブルの住所自体を更新する仕様にするね）
        $user->profile->update([
            // ★ここも 'postal_code' を受け取って 'zipcode' に入れる（統一！）
            'zipcode'       => $request->input('postal_code'),
            'address'       => $request->input('address'),
            'building_name' => $request->input('building'),
        ]);

        // 更新が終わったら、購入画面（purchase/3 とか）に戻る！
        return redirect('/purchase/' . $item_id);
    }
}