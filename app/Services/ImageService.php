<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected $manager;

    public function __construct()
    {
        $this->manager = new ImageManager(new Driver());
    }

    public function uploadImage(UploadedFile $image, string $path = 'products'): string
    {
        $fileName = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
        $fullPath = $path . '/' . $fileName;
        
        // Tạo và xử lý ảnh với version 3
        $img = $this->manager->read($image);
        $img->scale(width: 800);
        
        // Lưu ảnh đã optimize
        Storage::disk('public')->put($fullPath, $img->encode());
        
        return $fullPath;
    }

    public function deleteImage(string $path): bool
    {
        return Storage::disk('public')->delete($path);
    }
} 