@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
@endsection

@section('content')
<div class="register-form">
    <h2 class="main__title">会員登録</h2>

    <form action="/register" method="post">
        @csrf

        <div class="form__group">
            <label class="form__label" for="name">ユーザー名</label>
            <input class="form__input" type="text" name="name" id="name" value="{{ old('name') }}">
            @error('name')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="email">メールアドレス</label>
            <input class="form__input" type="text" name="email" id="email" value="{{ old('email') }}">
            @error('email')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="password">パスワード</label>
            <input class="form__input" type="password" name="password" id="password">
            @error('password')
                <p class="form__error">{{ $message }}</p>
            @enderror
        </div>

        <div class="form__group">
            <label class="form__label" for="password_confirmation">確認用パスワード</label>
            <input class="form__input" type="password" name="password_confirmation" id="password_confirmation">
        </div>

        <div class="form__group">
            <button class="form__button" type="submit">登録する</button>
        </div>
    </form>

    <div class="register__link">
        <a class="link__text" href="/login">ログインはこちら</a>
    </div>
</div>
@endsection