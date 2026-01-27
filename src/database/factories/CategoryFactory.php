<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition()
    {
        return [
            'content' => $this->faker->randomElement([
                'ファッション',
                '家電',
                'インテリア',
                'レディース',
                'メンズ',
                'コスメ',
                '本',
                'ゲーム',
                'スポーツ',
                'キッチン',
                'ハンドメイド',
                'アクセサリー',
                'おもちゃ',
                'ベビー・キッズ',
            ]),
        ];
    }
}
