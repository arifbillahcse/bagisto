{{--
    Just for You Grid Component (3 rows × 4 columns = 12 products)

    Uses the "JustForYouController" API which returns:
    - Featured products from the last-viewed category (if cookie exists)
    - Fallback: featured products globally (for new visitors)

    Props:
      - title: Section heading (default "Just for You")
      - navigationLink: URL for "View More" button
--}}

@props([
    'title' => 'Just for You',
    'navigationLink' => null,
])

<div class="container mt-20 max-lg:px-8 max-md:mt-8 max-sm:mt-7 max-sm:!px-4">
    <div class="flex justify-between items-center">
        <h2 class="font-dmserif text-3xl max-md:text-2xl max-sm:text-xl">
            {{ $title }}
        </h2>
    </div>

    {{-- 3 Rows × 4 Columns Grid --}}
    <div class="grid grid-cols-4 gap-8 mt-10 max-lg:grid-cols-3 max-md:gap-7 max-md:mt-5 max-sm:grid-cols-2 max-sm:gap-4">
        @forelse ($products as $product)
            <x-shop::products.card
                :product="$product"
            />
        @empty
            <p class="col-span-4 text-center py-10 text-gray-500">
                {{ __('shop::app.components.products.carousel.no-products') }}
            </p>
        @endforelse
    </div>

    {{-- View More Button --}}
    @if ($navigationLink && count($products) > 0)
        <div class="flex justify-center mt-8">
            <a
                href="{{ $navigationLink }}"
                class="secondary-button mx-auto block w-max rounded-2xl px-11 py-3 text-center text-base max-lg:mt-0 max-lg:py-3.5 max-md:rounded-lg"
            >
                View More
            </a>
        </div>
    @endif
</div>
