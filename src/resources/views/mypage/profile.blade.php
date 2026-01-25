@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="mypage-container">
        <div class="user-info">
            <div class="user-icon">
                @if (isset($profile->avatar_url))
                    <img src="{{ asset('storage/' . $profile->avatar_url) }}" alt="プロフィール画像" class="user-icon-img">
                @else
                    <div class="icon-dummy"></div>
                @endif
            </div>

            <h2 class="user-name">{{ $user->name }}</h2>
            <a href="{{ route('mypage.edit') }}" class="edit-btn">プロフィールを編集</a>
        </div>

        <div class="mypage-tabs">
            <a href="/mypage?page=sell"
                class="tab-item {{ request('page') == 'sell' || request('page') == null ? 'active' : '' }}">
                出品した商品
            </a>
            <a href="/mypage?page=buy" class="tab-item {{ request('page') == 'buy' ? 'active' : '' }}">
                購入した商品
            </a>
        </div>

        <div class="product-list">
            @if (request('page') == 'buy')
                @foreach ($purchasedItems as $purchase)
                    <div class="product-item">
                        {{-- ↓ class="product-item-link" を追加 --}}
                        <a href="{{ route('item.show', ['item_id' => $purchase->item->id]) }}" class="product-item-link">
                            <img src="{{ asset('storage/' . $purchase->item->img_url) }}" alt="商品画像"
                                class="product-img">
                            <p class="product-name">{{ $purchase->item->name }}</p>
                        </a>
                    </div>
                @endforeach
            @else
                @foreach ($soldItems as $item)
                    <div class="product-item">
                        {{-- ↓ class="product-item-link" を追加 --}}
                        <a href="{{ route('item.show', ['item_id' => $item->id]) }}" class="product-item-link">
                            <img src="{{ asset('storage/' . $item->img_url) }}" alt="商品画像" class="product-img">
                            <p class="product-name">{{ $item->name }}</p>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection