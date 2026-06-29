<?php

namespace App\Http\Controllers\Api;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class NewsController extends BaseCrudController
{
    protected string $modelClass = News::class;
    protected string $orderBy = 'published_at';
    protected string $orderDir = 'desc';
    protected array $imageFields = ['cover_image_url' => 'news'];

    /** Public listing: only published items (published_at <= now). */
    public function publicIndex(Request $request)
    {
        $q = $this->modelClass::query()
            ->where('published_at', '<=', Carbon::now())
            ->orderBy('published_at', 'desc');
        if ($request->boolean('paginate')) {
            return response()->json($q->paginate((int) $request->input('per_page', 12)));
        }
        return response()->json($q->get());
    }

    public function publicShow(int $id)
    {
        $item = $this->modelClass::where('published_at', '<=', Carbon::now())->findOrFail($id);
        return response()->json($item);
    }

    protected function storeRules(): array
    {
        return [
            'title'           => 'required|string',
            'title_en'        => 'nullable|string',
            'title_az'        => 'nullable|string',
            'cover_image_url' => 'required',
            'blocks'          => 'required',
            'published_at'    => 'required|date',
            'order_index'     => 'nullable|integer',
        ];
    }

    protected function updateRules(): array
    {
        return [
            'title'           => 'sometimes|string',
            'title_en'        => 'nullable|string',
            'title_az'        => 'nullable|string',
            'cover_image_url' => 'nullable',
            'blocks'          => 'sometimes',
            'published_at'    => 'sometimes|date',
            'order_index'     => 'nullable|integer',
        ];
    }

    /** Normalize blocks: accept array OR JSON string -> store as JSON text. */
    protected function beforeSave(array $data, Request $request, $existing): array
    {
        if (array_key_exists('blocks', $data)) {
            $blocks = $data['blocks'];
            if (is_string($blocks)) {
                $decoded = json_decode($blocks, true);
                $blocks  = json_last_error() === JSON_ERROR_NONE ? $decoded : $blocks;
            }
            // Eloquent 'array' cast will json_encode on save.
            $data['blocks'] = $blocks;
        }
        return $data;
    }
}
