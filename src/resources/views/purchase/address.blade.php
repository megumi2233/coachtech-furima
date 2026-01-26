@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/address.css') }}">
@endsection

@section('content')
    <div class="address-main">
        <h2 class="address-title">住所の変更</h2>

        <form class="address-form" action="{{ route('purchase.address.update', ['item_id' => $itemId]) }}" method="post">
            @csrf

            <div class="address-form-group">
                <label class="address-form-label" for="postal-code">郵便番号</label>
                <input type="text" id="postal-code" name="postal_code" class="address-form-input"
                    value="{{ old('postal_code', Auth::user()->profile->zipcode) }}">
                @error('postal_code')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-form-group">
                <label class="address-form-label" for="address">住所</label>
                <input type="text" id="address" name="address" class="address-form-input"
                    value="{{ old('address', Auth::user()->profile->address) }}">
                @error('address')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <div class="address-form-group">
                <label class="address-form-label" for="building">建物名</label>
                <input type="text" id="building" name="building" class="address-form-input"
                    value="{{ old('building', Auth::user()->profile->building_name) }}">
                @error('building')
                    <p class="error-message">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="address-form-btn">更新する</button>
        </form>
    </div>
@endsection
