<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Upload\UploadRequest;
use App\Services\Admin\UploadService;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    public function __construct(
        protected UploadService $uploadService
    )
    {
    }

    public function upload(UploadRequest $request)
    {
        $file = $request->file('file');
        $url = $this->uploadService->upload($file);

        if (is_null($url)) {
            return response()->json([
                'success' => false,
                'message' => 'Файл не был загружен'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }
}
