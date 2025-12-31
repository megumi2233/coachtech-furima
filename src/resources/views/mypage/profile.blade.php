@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
@endsection

@section('content')
    <div class="mypage-container">
        <div class="user-info">
            <div class="user-icon">
                <div class="icon-dummy"></div>
            </div>
            <h2 class="user-name">ユーザー名</h2>
            <a href="/mypage/profile" class="edit-btn">プロフィールを編集</a>
        </div>

        <div class="mypage-tabs">
            <a href="/mypage?tab=sell" class="tab-item active">出品した商品</a>
            <a href="/mypage?tab=buy" class="tab-item">購入した商品</a>
        </div>

        <div class="product-list">
            <div class="product-item">
                <div class="product-img">商品画像</div>
                <p class="product-name">商品名</p>
            </div>
            <div class="product-item">
                <div class="product-img">商品画像</div>
                <p class="product-name">商品名</p>
            </div>
            <div class="product-item">
                <div class="product-img">商品画像</div>
                <p class="product-name">商品名</p>
            </div>
        </div>
    </div>
@endsection
