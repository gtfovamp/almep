<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;

class ImageUploadController extends Controller
{
    public function upload(Request $request, ImageUploadService $service)
    {
        $request->validate([
            'file'   => 'required|image|max:20480',
            'folder' => 'nullable|string|max:64',
        ]);
        $folder = preg_replace('/[^a-z0-9_-]/i', '', $request->input('folder', 'misc')) ?: 'misc';
        try {
            $url = $service->store($request->file('file'), $folder);
            return response()->json(['url' => $url]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
