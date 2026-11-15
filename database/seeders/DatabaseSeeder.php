<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Demo data: one account per role (password: "password"), 10 products, 20 orders.
     */
    public function run(): void
    {
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@scm.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        $supplier = User::create([
            'name' => 'ABC Supplies',
            'email' => 'supplier@scm.com',
            'password' => bcrypt('password'),
            'role' => 'supplier',
            'nationality' => 'Saudi Arabia',
        ]);

        $customer = User::create([
            'name' => 'Ahmed Mohamed',
            'email' => 'customer@scm.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
        ]);

        $catalog = [
            ['Industrial Drill', 'Electronics'], ['Office Chair', 'Furniture'], ['Safety Vest', 'Clothing'],
            ['Barcode Scanner', 'Electronics'], ['Storage Rack', 'Furniture'], ['Work Gloves', 'Clothing'],
            ['Label Printer', 'Electronics'], ['Packing Table', 'Furniture'], ['Hard Hat', 'Clothing'],
            ['Pallet Jack', 'Furniture'],
        ];
        $shipping = [['Land'], ['Air', 'Land'], ['Sea', 'Land'], ['Air', 'Sea', 'Land']];

        $products = collect($catalog)->map(fn ($item, $i) => Product::create([
            'user_id' => $supplier->id,
            'name' => $item[0],
            'sku' => 'SKU-' . str_pad($i + 1, 3, '0', STR_PAD_LEFT),
            'category' => $item[1],
            'stock_quantity' => rand(5, 50),
            'price' => rand(100, 1000),
            'sold_count' => 0,
            'shipping_method' => $shipping[$i % count($shipping)],
        ]));

        for ($i = 1; $i <= 20; $i++) {
            $product = $products->random();
            $quantity = rand(1, 5);

            Order::create([
                'user_id' => $customer->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'total_price' => $product->price * $quantity,
                'status' => ['pending', 'processing', 'shipped', 'delivered'][rand(0, 3)],
                'created_at' => now()->subDays(rand(1, 170)),
            ]);

            $product->increment('sold_count', $quantity);
        }
    }
}
