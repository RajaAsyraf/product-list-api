<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Enums\ProductTypeEnum;
use App\Models\ProductModel;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = collect([
            [
                'product_id' => 4450,
                'type' => 'Smartphone',
                'model' => 'iPhone SE',
                'capacity' => '2GB/16GB',
                'brand' => 'Apple',
                'quantity' => 13,
            ],
            [
                'product_id' => 4768,
                'type' => 'Smartphone',
                'model' => 'iPhone SE',
                'capacity' => '2GB/32GB',
                 'brand' => 'Apple',
                'quantity' => 30,
            ],
            [
                'product_id' => 4451,
                'type' => 'Smartphone',
                'model' => 'iPhone SE',
                'capacity' => '2GB/64GB',
                 'brand' => 'Apple',
                'quantity' => 20,
            ],
            [
                'product_id' => 4574,
                'type' => 'Smartphone',
                'model' => 'iPhone SE',
                'capacity' => '2GB/128GB',
                 'brand' => 'Apple',
                'quantity' => 16,
            ],
            [
                'product_id' => 6039,
                'type' => 'Smartphone',
                'model' => 'iPhone SE (2020)',
                'capacity' => '3GB/64GB',
                 'brand' => 'Apple',
                'quantity' => 18,
            ],
        ]);

        $products->each(function ($productData) {
            $brand = Brand::firstOrCreate([
                'name' => $productData['brand']
            ]);
            $productModel = ProductModel::firstOrCreate([
                'name' => $productData['model'],
                'brand_id' => $brand->id,
            ]);
            $productModel->products()->create([
                'product_id' => $productData['product_id'],
                'type' => ProductTypeEnum::from($productData['type'])->value,
                'capacity' => $productData['capacity'],
                'quantity' => $productData['quantity'],
            ]);
        });
    }
}
