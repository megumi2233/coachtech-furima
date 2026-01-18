<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    public function authorize()
    {
        return true; 
    }

    public function rules()
    {
        return [
            
            'postal_code' => ['required', 'regex:/^[0-9]{3}-[0-9]{4}$/'], 
            'address'     => ['required'], 
            'building'    => ['nullable'], 
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