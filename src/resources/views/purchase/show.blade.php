\@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/purchase.css') }}">
@endsection

@section('content')
    <div class="purchase-container">
        <form class="purchase-form" action="/purchase/{{ $item->id }}" method="post">
            @csrf

            <div class="purchase-left">
                <div class="purchase-item">
                    <div class="purchase-item-image">
                        <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}">
                    </div>
                    <div class="purchase-item-info">
                        <h2 class="purchase-item-name">{{ $item->name }}</h2>
                        <p class="purchase-item-price">¥{{ number_format($item->price) }}</p>
                    </div>
                </div>

                <div class="purchase-section">
                    <h3 class="purchase-section-title">支払い方法</h3>
                    <div class="purchase-section-content">
                        <select class="payment-select" name="payment_method" id="payment-method"
                            onchange="updatePaymentInfo()">
                            <option value="" selected disabled hidden>選択してください</option>
                            <option value="konbini">コンビニ支払い</option>
                            <option value="card">カード支払い</option>
                        </select>
                        @error('payment_method')
                            <div class="error-message"
                                style="color: #ff5555; font-size: 14px; margin-top: 5px; font-weight: bold;">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                </div>

                <div class="purchase-section">
                    <div class="section-header">
                        <h3 class="purchase-section-title">配送先</h3>
                        <a class="address-change-link" href="/purchase/address/{{ $item->id }}">変更する</a>
                    </div>
                    <div class="purchase-section-content">
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
                        <span class="summary-value" id="selected-payment">選択してください</span>
                    </div>
                </div>
                <button class="purchase-button" type="submit">購入する</button>
            </div>
        </form>
    </div>

    <script>
        function updatePaymentInfo() {
            const select = document.getElementById('payment-method');
            const display = document.getElementById('selected-payment');
            const selectedText = select.options[select.selectedIndex].text;
            display.textContent = selectedText;
        }
    </script>
@endsection
