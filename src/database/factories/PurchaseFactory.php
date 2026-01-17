<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Item;

class PurchaseFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'item_id' => Item::factory(),

            'payment_method' => $this->faker->randomElement(['コンビニ支払い', 'カード支払い']),
            'shipping_postal_code' => $this->faker->postcode, 
            'shipping_address' => $this->faker->prefecture . $this->faker->city . $this->faker->streetAddress, 
            'shipping_building_name' => $this->faker->secondaryAddress,
        ];
    }
}
