<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        Product::upsert([
            [
                'name' => 'Round Container - Deliver',
                'price' => 35.00,
                'stock_quantity' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Gallon Container with Faucet',
                'price' => 50.00,
                'stock_quantity' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Round Gallon',
                'price' => 25.00,
                'stock_quantity' => 100,
                'is_active' => true,
            ],
        ], ['name'], ['price', 'stock_quantity', 'is_active']);
    }
} 