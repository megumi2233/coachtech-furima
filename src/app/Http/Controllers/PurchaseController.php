<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth; // ★ユーザー情報を取るために追加
use App\Models\Purchase;

class PurchaseController extends Controller
{
    // 商品購入画面を表示する係
    public function create($item_id)
    {
        $item = Item::findOrFail($item_id);
        return view('purchase.show', compact('item'));
    }

    // 購入ボタンが押された時の仕事（Stripeへ案内！）
    public function store(PurchaseRequest $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // ★追加：Stripeの秘密鍵をセット
        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        // ★追加：フォームから送られてきた支払い方法を取得
        // （"konbini" か "card" が入っているはず！）
        $paymentMethod = $request->input('payment_method');

        // ★追加：Stripeに渡す「支払い方法のリスト」を作る
        $paymentMethodTypes = [];
        if ($paymentMethod === 'konbini') {
            $paymentMethodTypes = ['konbini']; // コンビニ払い
        } else {
            $paymentMethodTypes = ['card'];    // カード払い
        }

        // ★追加：コンビニ払いには「お客様のメールアドレス」が必須なので取得する
        // （ログイン中のユーザー情報を取得）
        $user = Auth::user();

        // 決済セッションを作成する
        $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => $paymentMethodTypes, // ★ここで切り替える！
            
            // 支払い情報
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            
            // ★重要：コンビニ払いの場合、お客様のメールアドレスをStripeに伝える必要がある
            'customer_email' => $user->email, 

            // 成功したら success メソッドへ！（支払い方法も一緒に渡すよ）
            'success_url' => route('purchase.success', ['item_id' => $item->id]) . '?payment_method=' . $paymentMethod,
            
            // キャンセルしたらどこに戻る？ ➡ 商品詳細ページへ
            'cancel_url' => route('item.show', $item->id),
        ]);

        // Stripeの決済画面へジャンプ！
        return redirect($checkout_session->url);
    }

    // ★追加：決済成功時の処理（データを保存する）
    public function success(Request $request, $item_id)
    {
        // 1. 商品とユーザーの情報を取得
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // 2. Stripeから戻ってきたときに持っている「支払い方法」を受け取る
        $paymentMethod = $request->input('payment_method');

        // 3. purchasesテーブルに保存！
        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $paymentMethod,
            'shipping_postal_code' => $user->profile->zipcode,
            'shipping_address' => $user->profile->address,
            'shipping_building_name' => $user->profile->building_name,
        ]);

        // 4. 完了したらトップページ（商品一覧）へ戻る
        return redirect('/');
    }
}