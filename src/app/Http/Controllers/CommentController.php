<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Comment;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\CommentRequest; // ★さっき作った検査係を呼び出す！

class CommentController extends Controller
{
    // ここで Request ではなく CommentRequest を使うのがポイント！
    public function store(CommentRequest $request, $item_id)
    {
        // 1. ログインチェックはミドルウェアでもやるけど念のため
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. データ保存（バリデーションは CommentRequest が勝手にやってくれてるよ！）
        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item_id,
            'content' => $request->comment,
        ]);

        // 3. 元の画面に戻る
        return redirect()->back();
    }
}
