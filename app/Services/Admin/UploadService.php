<?php

namespace App\Services\Admin;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    public function upload(UploadedFile $file): ?string
    {
        $path = $file->store('uploads', 'public');

        if (!$path) {
            return null;
        }

        return asset(Storage::url($path));
    }
}
