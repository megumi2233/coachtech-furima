@extends('layouts.auth')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-form">
    <h2 class="main__title">ログイン</h2>

    <form action="/login" method="post">
        @csrf

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
            <button class="form__button" type="submit">ログインする</button>
        </div>
    </form>

    <div class="login__link">
        <a class="link__text" href="/register">会員登録はこちら</a>
    </div>
</div>
@endsection