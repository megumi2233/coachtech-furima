@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
<div class="product-list">
    <div class="product-list__header">
        <a href="/?tab=recommend&keyword={{ request('keyword') }}" class="tab {{ !request('tab') || request('tab') == 'recommend' ? 'active' : '' }}">
            おすすめ
        </a>
        <a href="/?tab=mylist&keyword={{ request('keyword') }}" class="tab {{ request('tab') == 'mylist' ? 'active' : '' }}">
            マイリスト
        </a>
    </div>

    <div class="product-list__content">
        {{-- 
            ▼ 設計書（FN015）の指示通り、「未認証（ログアウト）」かつ「マイリスト」の時は
               「何も表示しない」という動きにするよ！ 
        --}}
        @if (request('tab') == 'mylist' && !Auth::check())
            {{-- ここには何も書かない（空っぽにする） --}}
        @else
            {{-- 商品がある場合のみループして表示 --}}
            @foreach ($items as $item)
                <div class="product-card">
                    <a href="/item/{{ $item->id }}" class="product-card_link">
                        <div class="product-card_img-wrapper">
                            {{-- 画像表示 --}}
                            <img src="{{ asset('storage/' . $item->img_url) }}" alt="商品画像">
                            
                            {{-- SOLD表示 --}}
                            @if ($item->purchase)
                                <div class="sold-label">SOLD</div>
                            @endif
                        </div>
                        <div class="product-card_body">
                            <p class="product-card_name">{{ $item->name }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif 
    </div>
</div>
@endsection