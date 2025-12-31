@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/profile_edit.css') }}">
@endsection

@section('content')
<div class="profile-setting-container">
    <h2 class="page-title">プロフィール設定</h2>

    <form class="profile-form" action="/mypage/profile" method="post" enctype="multipart/form-data">
        @csrf
        
        <div class="profile-img-area">
            {{-- ▼ 画像があれば表示、なければグレーの円を表示 --}}
            <div class="current-icon">
                @if(isset($profile->img_url))
                    <img src="{{ asset('storage/' . $profile->img_url) }}" alt="プロフィール画像" class="profile-icon-img">
                @endif
            </div>
            
            <label class="upload-btn">
                画像を選択する
                <input type="file" name="profile_image" style="display: none;" onchange="previewImage(this);">
            </label>
        </div>
        {{-- エラーメッセージ --}}
        @error('profile_image')
            <p class="error-message">{{ $message }}</p>
        @enderror

        <div class="form-group">
            <label for="name">ユーザー名</label>
            {{-- ▼ 初回は空、2回目は名前が入る魔法の書き方 --}}
            <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}">
            @error('name')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="postcode">郵便番号</label>
            {{-- ▼ nameを "postal_code" から "postcode" に修正しました！ --}}
            <input type="text" id="postcode" name="postcode" value="{{ old('postcode', $profile->zipcode ?? '') }}">
            @error('postcode')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="address">住所</label>
            <input type="text" id="address" name="address" value="{{ old('address', $profile->address ?? '') }}">
            @error('address')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label for="building">建物名</label>
            <input type="text" id="building" name="building" value="{{ old('building', $profile->building_name ?? '') }}">
            @error('building')
                <p class="error-message">{{ $message }}</p>
            @enderror
        </div>

        <button class="update-btn" type="submit">更新する</button>
    </form>
</div>

{{-- ▼ 画像を選んだ瞬間にプレビューするスクリプト（おまけ） --}}
<script>
function previewImage(obj) {
    var fileReader = new FileReader();
    fileReader.onload = (function() {
        var currentIcon = document.querySelector('.current-icon');
        // 既存の中身を空にして、新しい画像を入れる
        currentIcon.innerHTML = ''; 
        var img = document.createElement('img');
        img.src = fileReader.result;
        img.classList.add('profile-icon-img');
        currentIcon.appendChild(img);
    });
    fileReader.readAsDataURL(obj.files[0]);
}
</script>
@endsection