<?php

namespace App\Services;

use App\Jobs\ProcessProductImage;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductService
{
    protected $productRepository;
    protected $imageService;

    public function __construct(
        ProductRepositoryInterface $productRepository,
        ImageService $imageService
    ) {
        $this->productRepository = $productRepository;
        $this->imageService = $imageService;
    }

    public function getAllProducts(): Collection
    {
        return $this->productRepository->all();
    }

    public function getProductById($id): Model
    {
        return $this->productRepository->find($id);
    }

    public function createProduct(array $data)
    {
        // Tạo product trước với ảnh tạm hoặc null
        $product = $this->productRepository->create(
            array_merge($data, ['image' => null])
        );

        // Nếu có ảnh, lưu tạm và dispatch job
        if (isset($data['image'])) {
            $tempPath = time() . '_' . $data['image']->getClientOriginalName();
            // Kiểm tra xem việc lưu file có thành công không
            if (Storage::put('temp/' . $tempPath, file_get_contents($data['image']))) {
                // Thêm thông báo để kiểm tra
                echo 'File đã được lưu tại: temp/' . $tempPath;
            } else {
                // Thêm thông báo lỗi nếu không lưu được
                echo 'Không thể lưu file.';
                exit;
            }
            // Xóa dòng echo '123'; và exit; để tiếp tục thực hiện
            ProcessProductImage::dispatch($product, $tempPath);
        }

        return $product;
    }

    public function updateProduct($id, array $data)
    {
        $product = $this->productRepository->find($id);

        // Nếu có ảnh mới
        if (isset($data['image'])) {
            ProcessProductImage::dispatch($product, $data['image']);
            unset($data['image']); // Không update ảnh ngay
        }

        return $this->productRepository->update($id, $data);
    }

    public function deleteProduct($id): bool
    {
        return $this->productRepository->delete($id);
    }
} 