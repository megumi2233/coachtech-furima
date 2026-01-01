<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AuthenticatedSessionController; 
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// ▼▼▼ Task 34 でここを修正しました！ ▼▼▼

// 会員登録画面を表示する
Route::get('/register', [RegisteredUserController::class, 'create']);

// 会員登録処理を行う（フォームの送信先）
Route::post('/register', [RegisteredUserController::class, 'store']);

// ▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲▲

// ▼▼▼ Task 38 でここを修正！ ▼▼▼

// ログイン画面を表示する
Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');

// ログイン処理を行う（フォームの送信先）
Route::post('/login', [AuthenticatedSessionController::class, 'store']);

// ▼▼▼ Task 42: これを追加！ ▼▼▼
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

// ▼▼▼ Task 43: プロフィール設定機能 ▼▼▼
// 設定画面を表示 (GET)
Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');

// 設定を保存 (POST)
Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

// ▼▼▼ Task 44: 商品一覧画面（トップページ）の設定 ▼▼▼
Route::get('/', [ItemController::class, 'index'])->name('root');

// 商品詳細画面を表示する
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');
