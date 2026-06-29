<?php

namespace App\Http\Controllers\Api;

use App\Models\Testimonial;
use Illuminate\Http\Request;

class TestimonialController extends BaseCrudController
{
    protected string $modelClass = Testimonial::class;

    /** Public listing: only approved testimonials. */
    public function publicIndex()
    {
        return response()->json(
            Testimonial::where('approved', 1)->orderBy('order_index')->get()
        );
    }

    /** Public: submit a testimonial (created unapproved). */
    public function publicStore(Request $request)
    {
        $data = $request->validate([
            'name'    => 'required|string|max:255',
            'name_en' => 'nullable|string',
            'name_az' => 'nullable|string',
            'text'    => 'required|string',
            'text_en' => 'nullable|string',
            'text_az' => 'nullable|string',
        ]);
        $data['approved'] = 0;
        $item = Testimonial::create($data);
        return response()->json(['message' => 'Спасибо! Отзыв отправлен на модерацию.', 'id' => $item->id], 201);
    }

    protected function storeRules(): array
    {
        return [
            'name'        => 'required|string',
            'name_en'     => 'nullable|string',
            'name_az'     => 'nullable|string',
            'text'        => 'required|string',
            'text_en'     => 'nullable|string',
            'text_az'     => 'nullable|string',
            'order_index' => 'nullable|integer',
            'approved'    => 'nullable|boolean',
        ];
    }

    protected function updateRules(): array
    {
        $r = $this->storeRules();
        $r['name'] = 'sometimes|string';
        $r['text'] = 'sometimes|string';
        return $r;
    }

    public function approve(int $id)
    {
        $t = Testimonial::findOrFail($id);
        $t->update(['approved' => 1]);
        return response()->json($t);
    }

    public function unapprove(int $id)
    {
        $t = Testimonial::findOrFail($id);
        $t->update(['approved' => 0]);
        return response()->json($t);
    }
}
