<?php

namespace Webkul\ShopExtension\Http\View\Composers;

use Illuminate\View\View;
use Webkul\Product\Repositories\ProductRepository;

class JustForYouComposer
{
    /**
     * Create a composer instance.
     */
    public function __construct(
        protected ProductRepository $productRepository,
    ) {}

    /**
     * Bind data to the view.
     *
     * Fetches 12 featured products, preferring the user's last-viewed category
     * (stored in cookie). Falls back to global featured products for new visitors.
     */
    public function compose(View $view): void
    {
        $categoryId = request()->cookie('last_viewed_category_id');

        // Start with featured products
        $query = $this->productRepository
            ->where('status', 1)
            ->where('featured', 1)
            ->with(['images', 'price_indices', 'categories']);

        // If category_id cookie exists → filter by that category
        if ($categoryId) {
            $query = $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }

        $products = $query
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();

        // If no products found in that category, fallback to global featured
        if ($products->isEmpty() && $categoryId) {
            $products = $this->productRepository
                ->where('status', 1)
                ->where('featured', 1)
                ->with(['images', 'price_indices', 'categories'])
                ->orderBy('created_at', 'desc')
                ->limit(12)
                ->get();
        }

        $view->with('justForYouProducts', $products);
    }
}
