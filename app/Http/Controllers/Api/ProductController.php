<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SyncProductSyncFileUploaded;
use App\Models\Product;
use App\Models\ProductSyncFile;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return response()->json(
            Product::with('productModel.brand')->paginate(10)
        );
    }

    public function syncProduct(Request $request)
    {
        request()->validate([
            'product_sync_file' => ['required', 'mimes:xlsx'],
        ]);

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

        return redirect()->route('home');
    }
}
