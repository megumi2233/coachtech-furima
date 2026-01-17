@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="purchase-container">
        <form class="purchase-form" action="/purchase/{{ $item->id }}" method="post">
            @csrf

            <div class="purchase-left">
                <div class="purchase-item">
                    <div class="purchase-item__image">
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    </div>
                    <div class="purchase-item__info">
                        <h2 class="purchase-item__name">{{ $item->name }}</h2>
                        <p class="purchase-item__price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <div class="purchase-section">
                    <h3 class="purchase-section__title">支払い方法</h3>
                    <div class="purchase-section__content">
                        {{-- ★変更1：id と onchange を追加！（センサーをつける） --}}
                        <select class="payment-select" name="payment_method" id="payment-method" onchange="updatePaymentInfo()">
                            <option value="" selected disabled hidden>選択してください</option>
                            <option value="konbini">コンビニ支払い</option>
                            <option value="card">カード支払い</option>
                        </select>
                    </div>
                </div>

                <div class="purchase-section">
                    <div class="section-header">
                        <h3 class="purchase-section__title">配送先</h3>
                        <a class="address-change-link" href="/purchase/address/{{ $item->id }}">変更する</a>
                    </div>
                    <div class="purchase-section__content">
                        <div class="address-info">
                            <p>〒 {{ Auth::user()->profile->zipcode }}</p>
                            <p>
                                {{ Auth::user()->profile->address }}
                                {{ Auth::user()->profile->building_name }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="purchase-right">
                <div class="summary-box">
                    <div class="summary-row">
                        <span class="summary-label">商品代金</span>
                        <span class="summary-price">¥{{ number_format($item->price) }}</span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">支払い方法</span>
                        {{-- ★変更2：idを追加して、最初は「選択してください」にしておく（名札をつける） --}}
                        <span class="summary-value" id="selected-payment">選択してください</span>
                    </div>
                </div>

                <button class="purchase-button" type="submit">購入する</button>
            </div>
        </form>
    </div>
    
    {{-- ★追加3：魔法のJavaScriptを一番下に追加！ --}}
    <script>
        function updatePaymentInfo() {
            // 1. プルダウン（左側）の要素を取得
            const select = document.getElementById('payment-method');
            
            // 2. 表示したい場所（右側）の要素を取得
            const display = document.getElementById('selected-payment');
            
            // 3. 今選ばれている「文字」を取り出す
            const selectedText = select.options[select.selectedIndex].text;
            
            // 4. 右側の文字を書き換える！
            display.textContent = selectedText;
        }
    </script>
@endsection