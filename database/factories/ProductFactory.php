<?php

namespace Database\Factories;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->name,
            'shop_id' => $this->faker->unique()->numberBetween(1,10),
            'price' => $this->faker->unique()->numberBetween(500, 50000),
            'stock' => $this->faker->unique()->numberBetween(500, 3000),
        ];
    }
}
