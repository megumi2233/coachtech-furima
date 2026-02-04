<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\PurchaseRequest;
use App\Models\Item;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function create($itemId)
    {
        $user = Auth::user();

        if (!$user->profile) {
            return redirect('/mypage/profile');
        }

        $item = Item::findOrFail($itemId);

        if ($item->purchase) {
            return redirect('/')->with('error', 'この商品はすでに売り切れです。');
        }

        return view('purchase.show', compact('item'));
    }

    public function store(PurchaseRequest $request, $itemId)
    {
        $item = Item::findOrFail($itemId);

        if ($item->purchase) {
            return redirect('/')->with('error', 'この商品はすでに売り切れです。');
        }

        \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $paymentMethod = $request->input('payment_method');
        $paymentMethodTypes = ($paymentMethod === 'konbini') ? ['konbini'] : ['card'];
        $user = Auth::user();

        $checkoutSession = \Stripe\Checkout\Session::create([
            'payment_method_types' => $paymentMethodTypes,
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
            'customer_email' => $user->email,
            'success_url' => route('purchase.success', ['item_id' => $item->id]) . '?payment_method=' . $paymentMethod,
            'cancel_url' => route('item.show', $item->id),
        ]);

        return redirect($checkoutSession->url);
    }

    public function success(Request $request, $itemId)
    {
        $item = Item::findOrFail($itemId);
        $user = Auth::user();
        $paymentMethod = $request->input('payment_method');

        Purchase::create([
            'user_id' => $user->id,
            'item_id' => $item->id,
            'payment_method' => $paymentMethod,
            'shipping_postal_code' => $user->profile->zipcode,
            'shipping_address' => $user->profile->address,
            'shipping_building_name' => $user->profile->building_name,
        ]);

        return redirect('/');
    }
}
