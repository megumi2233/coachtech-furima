<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;   // 👈 カテゴリを使うため
use App\Models\Condition;  // 👈 商品の状態を使うため
use App\Http\Requests\ExhibitionRequest; // 👈 バリデーション（関所）を使うため
use Illuminate\Support\Facades\Auth;

class ItemController extends Controller
{
    // ▼▼▼ 商品一覧を表示する機能 ▼▼▼
    public function index(Request $request)
    {
        $query = Item::query();

        // ▼▼▼ 追加：自分が出品した商品は表示しない！ ▼▼▼
        // ログインしている場合だけ、自分のIDを除外するよ
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }
        // ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

        // 1. 「マイリスト」タブが選ばれていた場合
        if ($request->query('tab') === 'mylist') {

            // ログインしていない場合
            if (!Auth::check()) {
                return view('index', ['items' => []]);
            }

            // ログインしている場合、「自分がいいねした商品」に絞り込む
            $user_id = Auth::id();
            $query->whereHas('likes', function ($q) use ($user_id) {
                $q->where('user_id', $user_id);
            });
        }

        // 2. 検索キーワードがある場合
        if ($keyword = $request->query('keyword')) {
            $query->where('name', 'LIKE', "%{$keyword}%");
        }

        // 3. データを取得
        $items = $query->orderBy('id', 'desc')->get();

        return view('index', compact('items'));
    }

    // ▼▼▼ 商品詳細を表示する機能 ▼▼▼
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('item.show', compact('item'));
    }

    // ▼▼▼ 【NEW】商品出品画面を表示する機能 ▼▼▼
    public function create()
    {
        // データベースからカテゴリと状態の一覧をもってくる
        $categories = Category::all();
        $conditions = Condition::all();

        // 画面にデータを渡して表示する
        return view('item.create', compact('categories', 'conditions'));
    }

    // ▼▼▼ 【NEW】商品を保存する機能 ▼▼▼
    public function store(ExhibitionRequest $request)
    {
        // 1. 画像を保存する
        // 'public' ディスクを使って保存する（これが正解！）
        $imagePath = $request->file('image')->store('item_images', 'public');

        // 2. 商品情報をデータベースに登録
        // ▼▼▼ 【重要】ここを修正！作った商品を $item という箱に入れるよ！ ▼▼▼
        $item = Item::create([
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'description' => $request->description,
            'price'       => $request->price,
            'img_url'     => $imagePath,
            'brand_name'  => $request->brand,
            'condition_id' => $request->condition_id,
        ]);

        // 3. カテゴリを登録（中間テーブル）
        // さっき箱に入れた $item に対して、カテゴリを紐付ける命令をするよ
        $item->categories()->sync($request->categories);

        // 4. トップページ（商品一覧）に戻る
        return redirect()->route('root');
    }
}
