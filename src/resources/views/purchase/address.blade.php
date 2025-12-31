@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-main">
    <h2 class="address-title">住所の変更</h2>

    <form class="address-form" action="/purchase/address/item/1" method="post">
        @csrf
        
        <div class="address-form__group">
            <label class="address-form__label" for="postal_code">郵便番号</label>
            <input type="text" id="postal_code" name="postal_code" class="address-form__input" value="123-4567">
        </div>

        <div class="address-form__group">
            <label class="address-form__label" for="address">住所</label>
            <input type="text" id="address" name="address" class="address-form__input" value="東京都渋谷区...">
        </div>

        <div class="address-form__group">
            <label class="address-form__label" for="building">建物名</label>
            <input type="text" id="building" name="building" class="address-form__input" value="マンション名">
        </div>

        <button type="submit" class="address-form__btn">更新する</button>
    </form>
</div>
@endsection