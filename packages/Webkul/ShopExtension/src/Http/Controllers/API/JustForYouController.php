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
     * Get "Just for You" products based on user's viewing history or as fallback (featured).
     *
     * - If the visitor has viewed a product recently (category id stored client-side in the
     *   `last_viewed_category_id` cookie) → return featured products from that category
     * - Else, or if that category has no featured products → return featured products globally
     *   (fallback for new visitors)
     *
     * `status`/`featured`/`created_at` live on the EAV-backed `product_flat` table, not
     * directly on `products`, so filtering goes through the repository's `getAll()` (the
     * same method the storefront product API uses) instead of a plain Eloquent `where()`.
     *
     * API: /api/products/just-for-you
     * Query params:
     *   - category_id: override category (defaults to the cookie value)
     *   - limit: how many products to return (default 12)
     */
    public function index(Request $request): JsonResource
    {
        $limit = (int) $request->query('limit', 12);
        $categoryId = $request->query('category_id', $request->cookie('last_viewed_category_id'));

        $baseParams = [
            'status'               => 1,
            'featured'             => 1,
            'visible_individually' => 1,
            'channel_id'           => core()->getCurrentChannel()->id,
            'limit'                => $limit,
            'sort'                 => 'created_at-desc',
        ];

        $products = collect();

        if ($categoryId) {
            $products = $this->productRepository
                ->getAll(array_merge($baseParams, ['category_id' => $categoryId]))
                ->getCollection();
        }

        if ($products->isEmpty()) {
            $products = $this->productRepository
                ->getAll($baseParams)
                ->getCollection();
        }

        return ProductCardResource::collection($products);
    }
}
