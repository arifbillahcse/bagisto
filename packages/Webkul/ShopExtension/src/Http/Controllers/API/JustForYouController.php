<?php

namespace Webkul\ShopExtension\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Shop\Http\Controllers\API\APIController;
use Webkul\Shop\Http\Resources\ProductCardResource;

class JustForYouController extends APIController
{
    /**
     * Create a controller instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
    ) {}

    /**
     * Get "Just for You" products based on user's viewing history or as fallback (featured/new).
     *
     * - If customer has viewed products in a category → return featured products from that category
     * - Else → return featured products (fallback for new visitors)
     *
     * API: /api/products/just-for-you
     * Query params:
     *   - category_id: override category (cookie value or param)
     *   - limit: how many products to return (default 12)
     */
    public function index(Request $request): JsonResource
    {
        $limit = (int) $request->query('limit', 12);
        $categoryId = $request->query('category_id');

        // Start with featured products (status=1, featured=1)
        $query = $this->productRepository
            ->where('status', 1)
            ->where('featured', 1)
            ->with(['images', 'price_indices']);

        // If category_id is provided (from cookie) → further filter by that category
        if ($categoryId) {
            $query = $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        $products = $query
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return ProductCardResource::collection($products);
    }
}
