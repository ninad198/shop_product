<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\Product;
use Faker\Factory as Faker;
use Illuminate\Container\Container;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    protected $faker;

    protected $count = 100;
    protected $productCount = 10;

    public function run()
    {
        $this->faker = Faker::create();
        Shop::factory()->count($this->count)->create()->each(function($parent) {
            for ($i=0; $i < $this->productCount; $i++) {
                Product::create([
                    "shop_id" => $parent->id,
                    'name' => $this->faker->unique()->name,
                    'price' => $this->faker->numberBetween(1, 5000),
                    'stock' => $this->faker->numberBetween(1, 800),
                ]);
            }
        });
    }
}
