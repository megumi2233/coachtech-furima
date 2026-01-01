<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoryItemTableSeeder extends Seeder
{
    public function run()
    {
        // --- 1. 腕時計 (Rolex) ---
        // ファッション、メンズ
        DB::table('category_item')->insert([
            ['item_id' => 1, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 1, 'category_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 2. HDD (東芝) ---
        // 家電
        DB::table('category_item')->insert([
            ['item_id' => 2, 'category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 3. 玉ねぎ3束 ---
        // キッチン (食べ物カテゴリがないのでキッチンにしました)
        DB::table('category_item')->insert([
            ['item_id' => 3, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 4. 革靴 ---
        // ファッション、メンズ
        DB::table('category_item')->insert([
            ['item_id' => 4, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 4, 'category_id' => 5, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 5. ノートPC ---
        // 家電
        DB::table('category_item')->insert([
            ['item_id' => 5, 'category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 6. マイク ---
        // 家電 (またはおもちゃ？一旦家電にしました)
        DB::table('category_item')->insert([
            ['item_id' => 6, 'category_id' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 7. ショルダーバッグ ---
        // ファッション、レディース
        DB::table('category_item')->insert([
            ['item_id' => 7, 'category_id' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 7, 'category_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 8. タンブラー ---
        // キッチン、インテリア
        DB::table('category_item')->insert([
            ['item_id' => 8, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 8, 'category_id' => 3, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 9. コーヒーミル ---
        // キッチン
        DB::table('category_item')->insert([
            ['item_id' => 9, 'category_id' => 10, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // --- 10. メイクセット ---
        // コスメ、レディース
        DB::table('category_item')->insert([
            ['item_id' => 10, 'category_id' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['item_id' => 10, 'category_id' => 4, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
