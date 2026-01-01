<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. ユーザー (親)
        $this->call(UsersTableSeeder::class);

        // 2. コンディション (親)
        $this->call(ConditionsTableSeeder::class);

        // 3. カテゴリー (親) ← ★これを追加！部活を作る！
        $this->call(CategoriesTableSeeder::class);

        // 4. 商品 (子)
        $this->call(ItemsTableSeeder::class);

        // 5. 商品とカテゴリーをつなぐ (中間テーブル) ← ★これを追加！入部届を出す！
        $this->call(CategoryItemTableSeeder::class);
    }
}
