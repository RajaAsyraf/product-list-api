<?php

namespace App\Services;

use App\Enums\ProductStatusEnum;
use App\Enums\ProductTypeEnum;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

class ProductService
{
    /**
     * Sync product data
     *
     * @param Collection $rows
     * @return void
     */
    public static function syncProduct(Collection $rows): void
    {
        $rows->each(function ($row) {
            $data = [
                'product_id' => $row[0],
                'type' => $row[1],
                'brand' => $row[2],
                'model' => $row[3],
                'capacity' => $row[4],
                'status' => $row[5],
            ];

            $product = Product::where('product_id', $data['product_id'])->first();

            if (! $product?->exists()) {
                $product = self::createProduct($data);
            }

            if ($data['status'] === ProductStatusEnum::SOLD->value) {
                $product->update(['quantity' => $product->quantity - 1]);
            } else if ($data['status'] === ProductStatusEnum::BUY->value) {
                $product->update(['quantity' => $product->quantity + 1]);
            }
            
            \Log::info("Updated a product ({$product->product_id}) status {$data['status']}. Current quantity is {$product->quantity}.");
        });
    }

    /**
     * Create product with model & brand
     *
     * @param array<string> $data
     * @return Product
     */
    public static function createProduct(array $data): Product
    {
        Validator::make(
            [
                'brand'         => $data['brand'] ?? null,
                'model'         => $data['model'] ?? null,
                'product_id'    => $data['product_id'] ?? null,
                'type'          => $data['type'] ?? null,
                'capacity'      => $data['product_id'] ?? null,
                'quantity'      => $data['quantity'] ?? null,
            ],
            [
                'brand'         => ['required', 'max:255'],
                'model'         => ['required', 'max:255'],
                'product_id'    => ['required', 'integer', 'max:99999'],
                'type'          => ['required', 'max:255'],
                'capacity'      => ['required', 'max:255'],
                'quantity'      => ['nullable', 'integer'],
            ]
        )->validate();

        return Brand::firstOrCreate([
            'name' => $data['brand'],
        ])
        ->productModels()->firstOrCreate([
            'name' => $data['model'],
        ])
        ->products()->firstOrCreate([
            'product_id' => $data['product_id'],
            'type' => ProductTypeEnum::from($data['type'])->value,
            'capacity' => $data['capacity'],
            'quantity' => $data['quantity'] ?? 0,
        ]);
    }
}