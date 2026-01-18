@extends('layouts.auth')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/verify.css') }}">
@endsection

@section('content')
    <div class="verify-content">
 
        @if (session('message'))
            <div class="verify-content__alert">
                {{ session('message') }}
            </div>
        @endif

        <div class="verify-content__text">
            <p>
                登録していただいたメールアドレスに認証メールを送付しました。<br>
                メール認証を完了してください。
            </p>
        </div>

        <div class="verify-content__button">
            <a class="verify-button" href="http://localhost:8025" target="_blank">認証はこちらから</a>
        </div>

        <div class="verify-content__link">
            <a class="link__text" href="/email/resend">認証メールを再送する</a>
        </div>
    </div>
@endsection