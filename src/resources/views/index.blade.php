@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/index.css') }}">
@endsection

@section('content')
    <h1 class="visually-hidden">商品一覧</h1>
    <div class="product-list">
        <div class="product-list-header">
            <a href="/?tab=recommend&keyword={{ request('keyword') }}"
                class="tab {{ !request('tab') || request('tab') == 'recommend' ? 'active' : '' }}">
                おすすめ
            </a>
            <a href="/?tab=mylist&keyword={{ request('keyword') }}"
                class="tab {{ request('tab') == 'mylist' ? 'active' : '' }}">
                マイリスト
            </a>
        </div>

        <div class="product-list-content">
            @if (request('tab') == 'mylist' && !Auth::check())
            @else
                @foreach ($items as $item)
                    <div class="product-card">
                        <a href="/item/{{ $item->id }}" class="product-card-link">
                            <div class="product-card-img-wrapper">
                                <img src="{{ asset('storage/' . $item->img_url) }}" alt="商品画像">
                                @if ($item->purchase)
                                    <div class="sold-label">SOLD</div>
                                @endif
                            </div>
                            <div class="product-card-body">
                                <p class="product-card-name">{{ $item->name }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
