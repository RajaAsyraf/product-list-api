<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductSyncFileUploaded;
use App\Models\Product;
use App\Models\ProductSyncFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        request()->validate([
            'perPage' => ['nullable', 'integer'],
            'search' => ['nullable'],
        ]);

        $perPage = $request->perPage ?? 10;
        $searchValue = $request->search ?? null;

        $productQuery = Product::with('productModel.brand');
        if ($searchValue) {
            $productQuery
                ->orWhere('product_id', 'like', "%{$searchValue}%")
                ->orWhere('type', 'like', "%{$searchValue}%")
                ->orWhere('capacity', 'like', "%{$searchValue}%")
                ->orWhere('quantity', 'like', "%{$searchValue}%")
                ->orWhereHas('productModel', function ($query) use ($searchValue) {
                    $query->where('name', 'like', "%{$searchValue}%")
                        ->orWhereHas('brand', function ($query) use ($searchValue) {
                            $query->where('name', 'like', "%{$searchValue}%");
                        });
                });
        }

        return response()->json($productQuery->paginate($perPage));
    }

    public function syncProduct(Request $request)
    {
        Validator::make(
            [
                'product_sync_file' => $request->product_sync_file,
                'extension' => strtolower($request->product_sync_file->getClientOriginalExtension()),
            ],
            [
                'product_sync_file' => ['required'],
                'extension' => ['required', 'in:xlsx,xls'],
            ]
        )->validate();

        $filenameWithExt = $request->file('product_sync_file')->getClientOriginalName();
        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
        $extension = $request->file('product_sync_file')->getClientOriginalExtension();
        $fileNameToStore = $filename.'_'.time().'.'.$extension;
        $path = $request->file('product_sync_file')->storeAs('public/product_sync_files',$fileNameToStore);
        
        $file = ProductSyncFile::create([
            'filename' => $fileNameToStore,
            'path' => $path,
        ]);

        SyncProductSyncFileUploaded::dispatch($file);

        return response()->json([
            'message' => 'Success! The file has been uploaded!'
        ]);
    }
}
