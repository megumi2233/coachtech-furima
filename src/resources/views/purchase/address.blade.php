@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
<div class="address-main">
    <h2 class="address-title">住所の変更</h2>

    <form class="address-form" action="{{ route('purchase.address.update', ['item_id' => $item_id]) }}" method="post">
        @csrf
        
        <div class="address-form__group">
            <label class="address-form__label" for="postal_code">郵便番号</label>
            {{-- ▼ここが大事！ valueの中に old() や Auth::user()... を入れるよ --}}
            <input type="text" id="postal_code" name="postal_code" class="address-form__input" 
                   value="{{ old('postal_code', Auth::user()->profile->zipcode) }}">
            
            {{-- ▼ここがエラーを表示する魔法のコード！ --}}
            @error('postal_code')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-form__group">
            <label class="address-form__label" for="address">住所</label>
            <input type="text" id="address" name="address" class="address-form__input" 
                   value="{{ old('address', Auth::user()->profile->address) }}">
            @error('address')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <div class="address-form__group">
            <label class="address-form__label" for="building">建物名</label>
            <input type="text" id="building" name="building" class="address-form__input" 
                   value="{{ old('building', Auth::user()->profile->building_name) }}">
            @error('building')
                <p style="color: red;">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="address-form__btn">更新する</button>
    </form>
</div>
@endsection