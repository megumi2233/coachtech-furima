<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

class ProfileFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(), 
            'zipcode' => $this->faker->postcode, 
            'address' => $this->faker->address,  
            'building_name' => $this->faker->secondaryAddress, 
            'avatar_url' => '', 
        ];
    }
}
