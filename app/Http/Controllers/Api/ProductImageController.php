<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function __construct(protected ImageUploadService $images) {}

    /** GET /api/admin/products/{product}/images */
    public function index(int $productId)
    {
        $product = Product::findOrFail($productId);
        return response()->json($product->images()->get());
    }

    /**
     * POST /api/admin/products/{product}/images
     * Accepts multipart 'file' OR 'image_url' / 'url' string.
     */
    public function store(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);

        $request->validate([
            'alt'         => 'nullable|string',
            'alt_en'      => 'nullable|string',
            'alt_az'      => 'nullable|string',
            'is_primary'  => 'nullable|boolean',
            'order_index' => 'nullable|integer',
            'image_url'   => 'nullable|string',
            'url'         => 'nullable|string',
            'file'        => 'nullable|file',
        ]);

        $file = $request->file('file');
        $urlInput = $request->input('url') ?: $request->input('image_url');
        $path = $this->images->resolve($file, is_string($urlInput) ? $urlInput : null, 'products');

        if ($path === null) {
            return response()->json(['message' => 'Нужно передать файл (file) или ссылку (image_url).'], 422);
        }

        $isPrimary = $request->boolean('is_primary');
        if ($isPrimary) {
            // unset previous primary
            $product->images()->update(['is_primary' => 0]);
        }

        $image = ProductImage::create([
            'product_id'  => $product->id,
            'url'         => $path,
            'image_url'   => $path,
            'alt'         => $request->input('alt', ''),
            'alt_en'      => $request->input('alt_en', ''),
            'alt_az'      => $request->input('alt_az', ''),
            'is_primary'  => $isPrimary ? 1 : 0,
            'order_index' => (int) $request->input('order_index', 0),
        ]);

        return response()->json($image, 201);
    }

    /** PUT /api/admin/product-images/{id} */
    public function update(Request $request, int $id)
    {
        $image = ProductImage::findOrFail($id);
        $data = $request->validate([
            'alt'         => 'nullable|string',
            'alt_en'      => 'nullable|string',
            'alt_az'      => 'nullable|string',
            'is_primary'  => 'nullable|boolean',
            'order_index' => 'nullable|integer',
        ]);
        if ($request->boolean('is_primary')) {
            ProductImage::where('product_id', $image->product_id)->update(['is_primary' => 0]);
            $data['is_primary'] = 1;
        }
        $image->update($data);
        return response()->json($image);
    }

    /** DELETE /api/admin/product-images/{id} */
    public function destroy(int $id)
    {
        $image = ProductImage::findOrFail($id);
        $image->delete(); // model event removes physical file
        return response()->json(['message' => 'Изображение удалено.']);
    }
}
