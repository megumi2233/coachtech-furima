<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true; // ★ここをtrueにするのを忘れずに！
    }

    public function rules()
    {
        return [
            // バリデーション一覧（画像）の通りのルールだよ
            'postal_code' => ['required', 'regex:/^[0-9]{3}-[0-9]{4}$/'], // ハイフンあり8文字
            'address'     => ['required'], // 住所は必須
            'building'    => ['nullable'], // 建物名はなくてもOK（画像にはないけど、一般的になくてもいいからね）
        ];
    }

    public function messages()
    {
        return [
            'postal_code.required' => '郵便番号を入力してください',
            'postal_code.regex'    => '郵便番号はハイフンありの8文字で入力してください',
            'address.required'     => '住所を入力してください',
        ];
    }
}