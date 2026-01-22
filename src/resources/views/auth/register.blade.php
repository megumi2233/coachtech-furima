@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-form">
    <h2 class="main-title">会員登録</h2>

    <form action="/register" method="post">
        @csrf

        <div class="form-group">
            <label class="form-label" for="name">ユーザー名</label>
            <input class="form-input" type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="email">メールアドレス</label>
            <input class="form-input" type="text" name="email" id="email" value="{{ old('email') }}">
            @error('email')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password">パスワード</label>
            <input class="form-input" type="password" name="password" id="password">
            @error('password')
                <p class="form-error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label" for="password-confirmation">確認用パスワード</label>
            <input class="form-input" type="password" name="password_confirmation" id="password_confirmation">
        </div>

        <div class="form-group">
            <button class="form-button" type="submit">登録する</button>
        </div>
    </form>

    <div class="register-link">
        <a class="link-text" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection