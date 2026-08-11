{{--
    Just for You Grid Component (3 rows x 4 columns = 12 products)

    Uses the "JustForYouController" API (route: shop.api.products.just_for_you.index) which returns:
    - Featured products from the last-viewed category (read from the `last_viewed_category_id`
      cookie, written by the product view page)
    - Fallback: featured products globally (for new visitors / when the cookie is absent)

    Product cards (x-shop::products.card) are Vue components that only render correctly inside a
    Vue `v-for` scope, so this section follows the same Vue + API pattern as the other native
    homepage sections ("New Arrival", "Product on Sale") instead of looping over Blade `$product`
    objects directly.

    Props:
      - title: Section heading (default "Just for You")
      - src: API endpoint URL
      - navigationLink: URL for the "View More" button
--}}

@props([
    'title' => 'Just for You',
    'src',
    'navigationLink' => null,
])

<v-just-for-you-grid
    src="{{ $src }}"
    title="{{ $title }}"
    navigation-link="{{ $navigationLink ?? '' }}"
>
    <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
</v-just-for-you-grid>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-just-for-you-grid-template"
    >
        <div
            class="container mt-20 max-lg:px-8 max-md:mt-8 max-sm:mt-7 max-sm:!px-4"
            v-if="! isLoading && products.length"
        >
            <div class="flex justify-between">
                <h2 class="font-dmserif text-3xl max-md:text-2xl max-sm:text-xl">
                    @{{ title }}
                </h2>
            </div>

            <div class="grid grid-cols-4 gap-8 mt-10 max-lg:grid-cols-3 max-md:gap-7 max-md:mt-5 max-sm:grid-cols-2 max-sm:gap-4">
                <x-shop::products.card
                    v-for="product in products"
                />
            </div>

            <a
                :href="navigationLink"
                class="secondary-button mx-auto mt-8 block w-max rounded-2xl px-11 py-3 text-center text-base max-md:rounded-lg"
                :aria-label="title"
                v-if="navigationLink"
            >
                View More
            </a>
        </div>

        <template v-if="isLoading">
            <x-shop::shimmer.products.carousel :navigation-link="$navigationLink ?? false" />
        </template>
    </script>

    <script type="module">
        app.component('v-just-for-you-grid', {
            template: '#v-just-for-you-grid-template',

            props: [
                'src',
                'title',
                'navigationLink',
            ],

            data() {
                return {
                    isLoading: true,

                    products: [],
                };
            },

            mounted() {
                this.getProducts();
            },

            methods: {
                getProducts() {
                    this.$axios.get(this.src)
                        .then(response => {
                            this.isLoading = false;

                            this.products = response.data.data;
                        }).catch(error => {
                            this.isLoading = false;

                            console.log(error);
                        });
                },
            },
        });
    </script>
@endPushOnce
