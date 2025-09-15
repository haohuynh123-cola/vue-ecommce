<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        private ProductServiceInterface $productService
    ) {}

    /**
     * Display a listing of products
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $filters = $request->only([
                'category_id', 'is_featured', 'min_price', 'max_price', 'search', 'per_page'
            ]);

            $products = $this->productService->getWithFilters($filters);

            return response()->json([
                'success' => true,
                'data' => $products,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(int $id): JsonResponse
    {
        try {
            $product = $this->productService->getById($id);

            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'product' => $product,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch product',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get featured products
     */
    public function featured(): JsonResponse
    {
        try {
            $products = $this->productService->getFeatured();

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch featured products',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'q' => 'required|string|min:2',
        ]);

        try {
            $products = $this->productService->searchProducts($request->q);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get products by category
     */
    public function byCategory(int $categoryId): JsonResponse
    {
        try {
            $products = $this->productService->getByCategory($categoryId);

            return response()->json([
                'success' => true,
                'data' => [
                    'products' => $products,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch products by category',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}