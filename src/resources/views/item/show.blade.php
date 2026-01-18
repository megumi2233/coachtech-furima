@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
    <div class="item-detail">
        <div class="item-detail__image">
            <img src="{{ asset('storage/' . $item->img_url) }}" alt="{{ $item->name }}" class="item-detail__img">
            @if ($item->purchase)
                <div class="sold-label">SOLD</div>
            @endif
        </div>

        <div class="item-detail__info">
            <h2 class="item-detail__name">{{ $item->name }}</h2>
            <p class="item-detail__brand">{{ $item->brand_name }}</p>
            <p class="item-detail__price">¥{{ number_format($item->price) }} <span class="tax">(税込)</span></p>

            <div class="item-detail__reaction">
                <div class="reaction-item">
                    <form method="POST" action="{{ route('item.like', ['item_id' => $item->id]) }}">
                        @csrf
                        <button type="submit" class="reaction-btn">
                            @if (Auth::check() && $item->likes->contains('user_id', Auth::id()))
                                <img class="reaction-icon active" src="{{ asset('images/like.png') }}" alt="いいね">
                            @else
                                <img class="reaction-icon" src="{{ asset('images/like.png') }}" alt="いいね">
                            @endif
                        </button>
                    </form>
                    <span class="reaction-count">{{ $item->likes->count() }}</span>
                </div>
                <div class="reaction-item">
                    <img class="reaction-icon" src="{{ asset('images/comment.png') }}" alt="コメント">
                    <span class="reaction-count">{{ $item->comments->count() }}</span>
                </div>
            </div>

            <div class="item-buy-area">
                @if ($item->purchase)
                    <button class="item-detail__button disabled" disabled>売り切れました</button>
                @else
                    <form action="/purchase/{{ $item->id }}" method="get">
                        <button class="item-detail__button" type="submit">購入手続きへ</button>
                    </form>
                @endif
            </div>

            <div class="item-detail__description">
                <h3>商品説明</h3>
                <p class="description-text">{{ $item->description }}</p>
            </div>

            <div class="item-detail__meta">
                <h3>商品の情報</h3>
                <div class="meta-row">
                    <span class="meta-label">カテゴリー</span>
                    <div class="meta-tags">
                        @foreach ($item->categories as $category)
                            <span class="meta-tag">{{ $category->content }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="meta-row">
                    <span class="meta-label">商品の状態</span>
                    <span class="meta-value">{{ $item->condition->content }}</span>
                </div>
            </div>

            <div class="item-detail__comment">
                <h3>コメント ({{ $item->comments->count() }})</h3>
                @foreach ($item->comments as $comment)
                    <div class="comment-item">
                        <div class="comment-user">
                            <div class="user-icon">
                                <img src="{{ !empty($comment->user->profile?->avatar_url) ? asset('storage/' . $comment->user->profile->avatar_url) : asset('images/default-avatar.png') }}"
                                     alt="" class="user-icon-img">
                            </div>
                            <span class="user-name">{{ $comment->user->name }}</span>
                        </div>
                        <div class="comment-body">
                            {{ $comment->content }}
                        </div>
                    </div>
                @endforeach

                <div class="comment-form">
                    <h3>商品へのコメント</h3>
                    <form action="{{ route('item.comment', ['item_id' => $item->id]) }}" method="post">
                        @csrf
                        @error('comment')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        <textarea class="comment-input" name="comment" rows="5"></textarea>
                        <button class="comment-button" type="submit">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection