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

        // 3. 商品 (子)
        $this->call(ItemsTableSeeder::class);
    }
}