<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\RegisteredUserController;
use App\Http\Controllers\AuthenticatedSessionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ▼▼▼ ここに追加したよ！(会員登録とログインの指名) ▼▼▼

// ① 会員登録（めぐみさんの作ったコントローラーを使う！）
Route::get('/register', [RegisteredUserController::class, 'create'])
    ->middleware('guest')
    ->name('register');

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest');

// ② ログイン（めぐみさんの作ったコントローラーを使う！）
Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->middleware('guest')
    ->name('login');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

// ▲▲▲ 追加ここまで ▲▲▲


// ▼▼▼ 誰でも見れるページ ▼▼▼

// 商品一覧画面（トップページ）
Route::get('/', [ItemController::class, 'index'])->name('root');

// 商品詳細画面
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('item.show');

// ▼▼▼ 会員さん専用エリア（ログインしてないと入れない！） ▼▼▼
Route::middleware('auth')->group(function () {

    // ログアウト
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy']);

    // =========================================================
    // 📧 メール認証のルート
    // （ここは「まだ認証してない人」も通れないと困るから、外に出しておくよ！）
    // =========================================================

    // ① メール認証の案内画面（verify.blade.php）を表示
    Route::get('/email/verify', function () {
        return view('auth.verify');
    })->name('verification.notice');

    // ② メール内のリンクをクリックした時の処理
    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill(); // 認証完了！
        return redirect('/mypage/profile'); // 終わったらプロフィール画面へ
    })->middleware('signed')->name('verification.verify');

    // ③ 「認証メールを再送する」をクリックした時の処理
    Route::get('/email/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', '認証メールを再送しました！');
    })->middleware('throttle:6,1')->name('verification.send');


    // =========================================================
    // 🔒 ここから下は「メール認証が終わった人」だけ通れるエリア！
    //    (middleware('verified') でブロックするよ！)
    // =========================================================
    Route::middleware('verified')->group(function () {

        // マイページ（プロフィール画面）を表示
        Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage.index');

        // プロフィール設定
        Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('mypage.edit');
        Route::post('/mypage/profile', [ProfileController::class, 'update'])->name('mypage.update');

        // いいね機能
        Route::post('item/{item_id}/like', [LikeController::class, 'like'])->name('item.like');

        // コメント送信機能
        Route::post('item/{item_id}/comment', [CommentController::class, 'store'])->name('item.comment');

        Route::get('/purchase/{item_id}', [PurchaseController::class, 'create'])->name('purchase.create');

        // 購入処理（ボタンを押した時）のルート
        Route::post('/purchase/{item_id}', [PurchaseController::class, 'store'])->name('purchase.store');

        // 購入完了後の処理
        Route::get('/purchase/success/{item_id}', [PurchaseController::class, 'success'])->name('purchase.success');

        // 配送先変更画面を表示する
        Route::get('/purchase/address/{item_id}', [ProfileController::class, 'editAddress'])->name('purchase.address.edit');

        // 変更した住所を保存して、購入画面に戻る
        Route::post('/purchase/address/{item_id}', [ProfileController::class, 'updateAddress'])->name('purchase.address.update');

        Route::get('/sell', [ItemController::class, 'create'])->name('item.create');
        Route::post('/sell', [ItemController::class, 'store'])->name('item.store');
    }); // ▲▲▲ 「メール認証済みエリア」の終わり ▲▲▲

});
