<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use Illuminate\Support\Facades\Auth; // 👈 これが超重要！（ログイン確認用）

class ItemController extends Controller
{
    public function index(Request $request)
    {
        // 1. まず「空っぽのクエリ（買い物カゴ）」を用意する
        $query = Item::query();

        // 2. 「マイリスト」タブが選ばれていた場合
        if ($request->query('tab') === 'mylist') {
            
            // ログインしていない場合
            if (!Auth::check()) {
                // 何も表示しないので、空っぽのリストを渡して終わりにする
                return view('index', ['items' => []]);
            }

            // ログインしている場合、「自分がいいねした商品」に絞り込む
            $user_id = Auth::id();
            $query->whereHas('likes', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        // 3. 検索キーワードがある場合（ここが今回の追加機能！）
        if ($keyword = $request->query('keyword')) {
            // 商品名（name）に、キーワードが含まれている（LIKE）ものを探す
            // % は「前後に何がついててもOK」という記号です
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        // 4. 全部の条件に合う商品をゲットする！
        $items = $query->get();

        return view('index', compact('items'));
    }
}