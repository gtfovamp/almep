<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductSpecification;
use Illuminate\Http\Request;

class ProductSpecificationController extends Controller
{
    public function index(int $productId)
    {
        $product = Product::findOrFail($productId);
        return response()->json($product->specifications()->get());
    }

    /** Single create. */
    public function store(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);
        $data = $this->rules($request);
        $data['product_id'] = $product->id;
        $spec = ProductSpecification::create($data);
        return response()->json($spec, 201);
    }

    /** Bulk create: body { specifications: [ {...}, ... ] } */
    public function bulkStore(Request $request, int $productId)
    {
        $product = Product::findOrFail($productId);
        $validated = $request->validate([
            'specifications'               => 'required|array',
            'specifications.*.key'         => 'required|string',
            'specifications.*.key_en'      => 'required|string',
            'specifications.*.key_az'      => 'required|string',
            'specifications.*.value'       => 'required|string',
            'specifications.*.value_en'    => 'required|string',
            'specifications.*.value_az'    => 'required|string',
            'specifications.*.order_index' => 'nullable|integer',
        ]);
        $created = [];
        foreach ($validated['specifications'] as $row) {
            $row['product_id'] = $product->id;
            $created[] = ProductSpecification::create($row);
        }
        return response()->json($created, 201);
    }

    public function update(Request $request, int $id)
    {
        $spec = ProductSpecification::findOrFail($id);
        $spec->update($this->rules($request, true));
        return response()->json($spec);
    }

    public function destroy(int $id)
    {
        $spec = ProductSpecification::findOrFail($id);
        $spec->delete();
        return response()->json(['message' => 'Характеристика удалена.']);
    }

    protected function rules(Request $request, bool $partial = false): array
    {
        $req = $partial ? 'sometimes' : 'required';
        return $request->validate([
            'key'         => $req . '|string',
            'key_en'      => $req . '|string',
            'key_az'      => $req . '|string',
            'value'       => $req . '|string',
            'value_en'    => $req . '|string',
            'value_az'    => $req . '|string',
            'order_index' => 'nullable|integer',
        ]);
    }
}
