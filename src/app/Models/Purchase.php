<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    // ★ここを変更！保存したい項目をすべてリストアップします
    protected $fillable = [
        'user_id',
        'item_id',
        'payment_method',         // 支払い方法
        'shipping_postal_code',   // 配送先郵便番号
        'shipping_address',       // 配送先住所
        'shipping_building_name', // 配送先建物名
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}