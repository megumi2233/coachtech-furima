@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/sell.css') }}">
@endsection

@section('content')
<div class="sell-container">
    <h2 class="page-title">商品の出品</h2>

    <form action="/sell" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-group">
            <label class="form-label">商品画像</label>
            <div class="image-upload-area">
                <label class="upload-btn">
                    画像を選択する
                    <input type="file" name="image" style="display: none;">
                </label>
            </div>
        </div>

        <h3 class="section-title">商品の詳細</h3>

        <div class="form-group">
            <label class="form-label">カテゴリー</label>
            <div class="category-list">
                <div class="category-item">
                    <input type="checkbox" id="cat1" name="categories[]" value="1" hidden>
                    <label for="cat1" class="category-label">ファッション</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat2" name="categories[]" value="2" hidden>
                    <label for="cat2" class="category-label">家電</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat3" name="categories[]" value="3" hidden>
                    <label for="cat3" class="category-label">インテリア</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat4" name="categories[]" value="4" hidden>
                    <label for="cat4" class="category-label">レディース</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat5" name="categories[]" value="5" hidden>
                    <label for="cat5" class="category-label">メンズ</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat6" name="categories[]" value="6" hidden>
                    <label for="cat6" class="category-label">コスメ</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat7" name="categories[]" value="7" hidden>
                    <label for="cat7" class="category-label">本</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat8" name="categories[]" value="8" hidden>
                    <label for="cat8" class="category-label">ゲーム</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat9" name="categories[]" value="9" hidden>
                    <label for="cat9" class="category-label">スポーツ</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat10" name="categories[]" value="10" hidden>
                    <label for="cat10" class="category-label">キッチン</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat11" name="categories[]" value="11" hidden>
                    <label for="cat11" class="category-label">ハンドメイド</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat12" name="categories[]" value="12" hidden>
                    <label for="cat12" class="category-label">アクセサリー</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat13" name="categories[]" value="13" hidden>
                    <label for="cat13" class="category-label">おもちゃ</label>
                </div>
                <div class="category-item">
                    <input type="checkbox" id="cat14" name="categories[]" value="14" hidden>
                    <label for="cat14" class="category-label">ベビー・キッズ</label>
                </div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label" for="condition">商品の状態</label>
            <div class="select-wrapper">
                <select name="condition" id="condition" class="form-control">
                    <option value="" selected hidden>選択してください</option>
                    <option value="良好">良好</option>
                    <option value="目立った傷や汚れなし">目立った傷や汚れなし</option>
                    <option value="やや傷や汚れあり">やや傷や汚れあり</option>
                    <option value="状態が悪い">状態が悪い</option>
                </select>
                <div class="select-arrow"></div> 
            </div>
        </div>

        <h3 class="section-title">商品名と説明</h3>

        <div class="form-group">
            <label class="form-label" for="name">商品名</label>
            <input type="text" id="name" name="name" class="form-control">
        </div>

        <div class="form-group">
            <label class="form-label" for="brand">ブランド名</label>
            <input type="text" id="brand" name="brand" class="form-control">
        </div>

        <div class="form-group">
            <label class="form-label" for="description">商品の説明</label>
            <textarea id="description" name="description" class="form-control" rows="5"></textarea>
        </div>

        <div class="form-group">
            <label class="form-label" for="price">販売価格</label>
            <div class="price-input-wrapper">
                <span class="currency-symbol">¥</span>
                <input type="number" id="price" name="price" class="form-control price-input">
            </div>
        </div>

        <button type="submit" class="submit-btn">出品する</button>
    </form>
</div>
@endsection