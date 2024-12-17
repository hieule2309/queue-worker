<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Services\ProductService;
use App\Traits\ResponseTrait;
use Exception;

class ProductController extends Controller
{
    use ResponseTrait;

    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        try {
            $products = $this->productService->getAllProducts();
            return $this->successResponse($products, 'Products retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            return $this->successResponse($product, 'Product retrieved successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();
            
            // Xử lý status từ form-data
            $data['status'] = filter_var($data['status'], FILTER_VALIDATE_BOOLEAN);
            
            // Nếu có file ảnh
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image');
            }

            $product = $this->productService->createProduct($data);
            
            return $this->successResponse($product, 'Product created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->updateProduct($id, $request->validated());
            return $this->successResponse($product, 'Product updated successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->productService->deleteProduct($id);
            return $this->successResponse(null, 'Product deleted successfully');
        } catch (Exception $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
} 