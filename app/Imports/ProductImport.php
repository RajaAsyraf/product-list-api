<?php

namespace App\Imports;

use App\Services\ProductService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    /**
     * @param Collection $rows
     * @return void
     */
    public function collection(Collection $rows): void
    {
        $rows->shift();
        ProductService::syncProduct($rows);
    }
}