<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item; // ★忘れずにこれを書く！（Itemモデルを使うよ、という宣言）

class ItemController extends Controller
{
    public function index()
    {
        // 1. データベースから全商品を取ってくる
        $items = Item::all();

        // 2. 画面（index）に $items を「お土産」として渡す
        return view('index', compact('items'));
    }
}
