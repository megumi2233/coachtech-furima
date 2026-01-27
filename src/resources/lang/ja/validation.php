<?php

return [
    'required' => ':attributeを入力してください',
    'email' => ':attributeはメール形式で入力してください',
    'max' => [
        'numeric' => ':attributeは:max以下で入力してください',
        'file' => ':attributeは:max KB以下のファイルを選択してください',
        'string' => ':attributeは:max文字以下で入力してください',
    ],
    'min' => [
        'numeric' => ':attributeは:min以上で入力してください',
        'file' => ':attributeは:min KB以上のファイルを選択してください',
        'string' => ':attributeは:min文字以上で入力してください',
    ],
    'unique' => ':attributeはすでに登録されています',
    'confirmed' => ':attributeと一致しません',
    'integer' => ':attributeは整数で入力してください',
    'string' => ':attributeは文字で入力してください',
    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'postal_code' => '郵便番号',
        'address' => '住所',
        'profile_image' => 'プロフィール画像',
    ],
];
