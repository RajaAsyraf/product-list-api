<?php

namespace App\Imports;

use App\Services\ProductService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ProductImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $rows->shift();
        ProductService::syncProduct($rows);
    }
}