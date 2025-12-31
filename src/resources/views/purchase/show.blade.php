@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="purchase-container">
        <form class="purchase-form" action="/purchase/item/1" method="post">
            @csrf

            <div class="purchase-left">
                <div class="purchase-item">
                    <div class="purchase-item__image">
                        <div class="item-dummy-img">商品画像</div>
                    </div>
                    <div class="purchase-item__info">
                        <h2 class="purchase-item__name">商品名</h2>
                        <p class="purchase-item__price">¥ 47,000</p>
                    </div>
                </div>

                <div class="purchase-section">
                    <h3 class="purchase-section__title">支払い方法</h3>
                    <div class="purchase-section__content">
                        <select class="payment-select" name="payment_method" id="payment_method">
                            <option value="" selected disabled hidden>選択してください</option>
                            <option value="konbini">コンビニ払い</option>
                            <option value="card">カード支払い</option>
                        </select>
                    </div>
                </div>

                <div class="purchase-section">
                    <div class="section-header">
                        <h3 class="purchase-section__title">配送先</h3>
                        <a class="address-change-link" href="/purchase/address/1">変更する</a>
                    </div>
                    <div class="purchase-section__content">
                        <div class="address-info">
                            <p>〒 XXX-YYYY</p>
                            <p>ここには住所と建物が入ります</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="purchase-right">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">商品代金</span>
                        <span class="summary-price">¥ 47,000</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">支払い方法</span>
                        <span class="summary-value">コンビニ払い</span>
                    </div>
                </div>

                <button class="purchase-button" type="submit">購入する</button>
            </div>
        </form>
    </div>
@endsection
