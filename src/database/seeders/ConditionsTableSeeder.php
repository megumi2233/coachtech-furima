<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConditionsTableSeeder extends Seeder
{
    public function run()
    {
        // 商品データのシートにある4つの状態を定義します
        $conditions = [
            '良好',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '状態が悪い',
        ];

        foreach ($conditions as $condition) {
            DB::table('conditions')->insert([
                'content' => $condition,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
