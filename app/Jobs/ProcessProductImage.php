<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ImageService;
use Illuminate\Support\Facades\Log;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessProductImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $product;
    protected $imagePath;

    public function __construct(Product $product, string $tempPath)
    {
        $this->product = $product;
        $this->imagePath = $tempPath;
    }

    public function handle(ImageService $imageService)
    {
        try {
            Log::info('Starting process image for product: ' . $this->product->id);
            // Đọc file từ storage tạm
            $file = new \Illuminate\Http\UploadedFile(
                storage_path('app/temp/' . $this->imagePath),
                $this->imagePath
            );

            // Upload và xử lý ảnh
            $imagePath = $imageService->uploadImage($file);
            
            // Cập nhật product với đường dẫn ảnh mới
            $this->product->update(['image' => $imagePath]);

            // Xóa file tạm
            // \Storage::delete('temp/' . $this->imagePath);

            Log::info('Image processed successfully');
        } catch (\Exception $e) {
            Log::error('Image processing failed: ' . $e->getMessage());
            throw $e;
        }
    }
} 