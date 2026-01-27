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
                <label class="image-upload-area">
                    <input type="file" name="image" class="file-input" onchange="previewImage(this);">
                    <span id="upload-text" class="upload-btn">画像を選択する</span>
                    <img id="preview-img" src="" class="preview-image" style="display: none;">
                </label>
                @error('image')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <h3 class="section-title">商品の詳細</h3>

            <div class="form-group">
                <label class="form-label">カテゴリー</label>
                <div class="category-list">
                    @foreach ($categories as $category)
                        <div class="category-item">
                            <input type="checkbox" id="cat{{ $category->id }}" name="categories[]"
                                value="{{ $category->id }}" hidden @if (in_array($category->id, old('categories', []))) checked @endif>
                            <label for="cat{{ $category->id }}" class="category-label">{{ $category->content }}</label>
                        </div>
                    @endforeach
                </div>
                @error('categories')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="condition">商品の状態</label>
                <div class="select-wrapper">
                    <select name="condition_id" id="condition" class="form-control">
                        <option value="" selected disabled hidden>選択してください</option>
                        @foreach ($conditions as $condition)
                            <option value="{{ $condition->id }}" @if (old('condition_id') == $condition->id) selected @endif>
                                {{ $condition->content }}
                            </option>
                        @endforeach
                    </select>
                    <div class="select-arrow"></div>
                </div>
                @error('condition_id')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <h3 class="section-title">商品名と説明</h3>

            <div class="form-group">
                <label class="form-label" for="name">商品名</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}">
                @error('name')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="brand">ブランド名</label>
                <input type="text" id="brand" name="brand" class="form-control" value="{{ old('brand') }}">
                @error('brand')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="description">商品の説明</label>
                <textarea id="description" name="description" class="form-control" rows="5">{{ old('description') }}</textarea>
                @error('description')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="price">販売価格</label>
                <div class="price-input-wrapper">
                    <span class="currency-symbol">¥</span>
                    <input type="number" id="price" name="price" class="form-control price-input"
                        value="{{ old('price') }}">
                </div>
                @error('price')
                    <div class="error-message">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="submit-btn">出品する</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var img = document.getElementById('preview-img');
                    img.src = e.target.result;
                    img.style.display = 'block';
                    document.getElementById('upload-text').style.display = 'none';
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
@endsection
