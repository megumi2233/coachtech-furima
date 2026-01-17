<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // 【重要！】ここを false から true に変えたよ。
        // 「true」にしないと、「あなたは権限がありません」って門前払いされちゃうからね。
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            // 画面の入力欄(name属性) => 'ルール'

            // 1. 商品画像：必須、画像ファイルであること、拡張子はjpgかpng
            'image' => 'required|image|mimes:jpeg,png',

            // 2. カテゴリ：必須（どれか1つ以上選んでね）
            'categories' => 'required',

            // 3. 商品の状態：必須（さっき name="condition_id" に直したから、ここも合わせるよ！）
            'condition_id' => 'required',

            // 4. 商品名：必須
            'name' => 'required',

            // 5. ブランド名：必須とは書いてないし、テーブルもNULL OKだから「nullable（なくてもいい）」にしたよ
            'brand' => 'nullable',

            // 6. 商品の説明：必須、最大255文字まで
            'description' => 'required|max:255',

            // 7. 価格：必須、数字であること、0円以上であること
            'price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'image.required' => '商品画像を選択してください',
            'image.mimes' => '画像はjpegまたはpng形式でアップロードしてください',
            'categories.required' => 'カテゴリーを選択してください',
            'condition_id.required' => '商品の状態を選択してください',
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明は255文字以内で入力してください',
            'price.required' => '販売価格を入力してください',
            'price.numeric' => '販売価格は数値で入力してください',
            'price.min' => '販売価格は0円以上で入力してください',
        ];
    }
}
