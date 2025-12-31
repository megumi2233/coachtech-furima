@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/item.css') }}">
@endsection

@section('content')
    <div class="item-detail">
        <div class="item-detail__image">
            <div class="item-dummy-img">商品画像</div>
        </div>

        <div class="item-detail__info">
            <h2 class="item-detail__name">商品名がここに入ります</h2>
            <p class="item-detail__brand">ブランド名</p>
            <p class="item-detail__price">¥47,000 <span class="tax">(税込)</span></p>

            <div class="item-detail__reaction">
                <div class="reaction-item">
                    <img class="reaction-icon" src="{{ asset('images/like.png') }}" alt="いいね">
                    <span class="reaction-count">3</span>
                </div>
                <div class="reaction-item">
                    <img class="reaction-icon" src="{{ asset('images/comment.png') }}" alt="コメント">
                    <span class="reaction-count">1</span>
                </div>
            </div>

            <form action="/purchase/1" method="get">
                <button class="item-detail__button" type="submit">購入手続きへ</button>
            </form>

            <div class="item-detail__description">
                <h3>商品説明</h3>
                <p class="description-text">
                    カラー：グレー<br>
                    新品<br>
                    商品の状態は良好です。傷もありません。<br>
                    購入後、即発送いたします。
                </p>
            </div>

            <div class="item-detail__meta">
                <h3>商品の情報</h3>
                <div class="meta-row">
                    <span class="meta-label">カテゴリー</span>
                    <div class="meta-tags">
                        <span class="meta-tag">洋服</span>
                        <span class="meta-tag">メンズ</span>
                    </div>
                </div>
                <div class="meta-row">
                    <span class="meta-label">商品の状態</span>
                    <span class="meta-value">良好</span>
                </div>
            </div>

            <div class="item-detail__comment">
                <h3>コメント (1)</h3>

                <div class="comment-item">
                    <div class="comment-user">
                        <div class="user-icon"></div> <span class="user-name">admin</span>
                    </div>
                    <div class="comment-body">
                        こちらはコメントが入ります。
                    </div>
                </div>

                <div class="comment-form">
                    <h3>商品へのコメント</h3>
                    <form action="" method="post">
                        @csrf
                        <textarea class="comment-input" name="comment" rows="5"></textarea>
                        <button class="comment-button" type="submit">コメントを送信する</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
