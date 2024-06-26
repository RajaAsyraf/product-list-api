<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductSyncFileUploaded;
use App\Models\Product;
use App\Models\ProductSyncFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Returns all products
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        request()->validate([
            'perPage' => ['nullable', 'integer'],
            'search' => ['nullable'],
        ]);

        $productQuery = Product::with('productModel.brand');

        if ($search = $request->search ?? null) {
            $productQuery
                ->orWhere('product_id', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhere('capacity', 'like', "%{$search}%")
                ->orWhere('quantity', 'like', "%{$search}%")
                ->orWhereHas('productModel', function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhereHas('brand', function ($query) use ($search) {
                            $query->where('name', 'like', "%{$search}%");
                        });
                });
        }

        return response()->json($productQuery->paginate($request->perPage ?? 10));
    }

    /**
     * Create and update product details in bulk
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function syncProduct(Request $request): JsonResponse
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
