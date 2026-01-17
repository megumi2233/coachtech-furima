<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Like;     // ★ Likeモデルを使うよ
use Illuminate\Support\Facades\Auth; // ★ ログイン情報を使うよ

class LikeController extends Controller
{
    // いいねをつける・外す機能
    public function like($item_id)
    {
        // 1. ログインしていないとダメ！
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. 誰が、どの商品に？
        $user_id = Auth::id();

        // 3. すでに「いいね」しているか探す
        $already_liked = Like::where('user_id', $user_id)->where('item_id', $item_id)->first();

        if ($already_liked) {
            // もし見つかったら -> 「いいね」を取り消す（削除）
            $already_liked->delete();
        } else {
            // もし見つからなかったら -> 「いいね」を登録する（作成）
            Like::create([
                'user_id' => $user_id,
                'item_id' => $item_id,
            ]);
        }

        // 4. 元の画面に戻る
        return redirect()->back();
    }
}
