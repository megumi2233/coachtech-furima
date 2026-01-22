<?php

namespace Database\Factories;

use App\Models\Condition;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ItemFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'condition_id' => Condition::factory(),

            'name' => $this->faker->randomElement([
                '腕時計',
                'HDD',
                '玉ねぎ3束',
                '革靴',
                'ノートPC',
                'マイク',
                'ショルダーバッグ',
                'タンブラー',
                'コーヒーミル',
                'メイクセット',
            ]),

            'brand_name' => $this->faker->company,
            'price' => $this->faker->numberBetween(500, 50000),
            'description' => $this->faker->realText(50),
            'img_url' => 'http://example.com/test.jpg',
        ];
    }
}