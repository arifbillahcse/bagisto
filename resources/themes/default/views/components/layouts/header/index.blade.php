{!! view_render_event('bagisto.shop.layout.header.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    /**
     * The custom header rows are driven by `footer_links` theme customizations that
     * are matched on their NAME. Create them under
     * Admin -> Settings -> Themes -> Create Theme (Type: Footer Links) using exactly
     * these names, and the header picks them up with no code change:
     *
     *   "Top Bar Contact" - phone / email shown on the left of the orange bar.
     *                       A url starting with `tel:` renders a phone icon, one
     *                       starting with `mailto:` renders an envelope icon.
     *   "Top Bar Links"   - links shown on the right of the orange bar.
     *   "Header Links"    - links shown in the grey navigation row.
     *
     * Each falls back to a sensible default below, so the header renders correctly
     * before any of these records exist.
     */
    $headerSections = $themeCustomizationRepository->findWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    $headerLinksByName = function ($name) use ($headerSections) {
        $record = $headerSections->firstWhere('name', $name);

        return collect($record?->options ?? [])
            ->flatMap(fn ($section) => $section)
            ->sortBy('sort_order')
            ->map(fn ($link) => ['title' => $link['title'], 'url' => $link['url']])
            ->values();
    };

    $topBarContact = $headerLinksByName('Top Bar Contact');

    if ($topBarContact->isEmpty()) {
        $topBarContact = collect([
            ['title' => '01779440297',      'url' => 'tel:01779440297'],
            ['title' => 'support@esoft.com', 'url' => 'mailto:support@esoft.com'],
        ]);
    }

    $topBarLinks = $headerLinksByName('Top Bar Links');

    if ($topBarLinks->isEmpty()) {
        $topBarLinks = collect([
            ['title' => 'Track Order',   'url' => route('shop.customers.account.orders.index')],
            ['title' => 'Customer Care', 'url' => route('shop.home.contact_us')],
        ]);
    }

    $headerNavLinks = $headerLinksByName('Header Links');

    if ($headerNavLinks->isEmpty()) {
        $headerNavLinks = collect([
            ['title' => 'Home',       'url' => route('shop.home.index')],
            ['title' => 'About Us',   'url' => route('shop.cms.page', 'about-us')],
            ['title' => 'Contact Us', 'url' => route('shop.home.contact_us')],
        ]);
    }

    /**
     * Shared inline styles. Structure is expressed inline rather than through a
     * class so the rows survive a stale or missing stylesheet; the <style> block
     * further down only adds hover colours and breakpoints.
     */
    $sTopLink = 'font-size:13px;color:#ffffff;text-decoration:none;white-space:nowrap;';
    $sNavLink = 'display:inline-block;padding:14px 0;font-size:14px;font-weight:600;color:#F4511E;text-decoration:none;white-space:nowrap;';
@endphp

<style>
    #rnjTopBar a:hover {
        opacity: .8;
    }

    #rnjNavBar a:hover {
        color: #c53d13 !important;
    }

    @media (max-width: 1023px) {
        #rnjTopBar,
        #rnjNavBar {
            display: none !important;
        }
    }
</style>

<!-- Utility bar -->
<div
    id="rnjTopBar"
    style="background-color:#F4511E;color:#ffffff;"
>
    <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px 24px;max-width:1600px;margin-left:auto;margin-right:auto;padding:8px 60px;">
        <!-- Contact details -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:20px;">
            @foreach ($topBarContact as $contact)
                <a
                    href="{{ $contact['url'] }}"
                    style="{{ $sTopLink }}display:inline-flex;align-items:center;gap:7px;"
                >
                    @if (str_starts_with($contact['url'], 'mailto:'))
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#ffffff"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            style="display:block;width:15px;height:15px;min-width:15px;flex:0 0 15px;"
                        >
                            <rect x="2" y="4" width="20" height="16" rx="2" />
                            <path d="M22 6l-10 7L2 6" />
                        </svg>
                    @else
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            width="15"
                            height="15"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="#ffffff"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            style="display:block;width:15px;height:15px;min-width:15px;flex:0 0 15px;"
                        >
                            <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    @endif

                    {{ $contact['title'] }}
                </a>
            @endforeach
        </div>

        <!-- Utility links -->
        <div style="display:flex;flex-wrap:wrap;align-items:center;gap:24px;">
            @foreach ($topBarLinks as $link)
                <a
                    href="{{ $link['url'] }}"
                    style="{{ $sTopLink }}"
                >
                    {{ $link['title'] }}
                </a>
            @endforeach
        </div>
    </div>
</div>

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="shadow-gray sticky top-0 z-10 bg-white shadow-sm max-lg:shadow-none">
    <v-header-switcher>
        <!-- Desktop Header Shimmer -->
        <div class="flex flex-wrap max-lg:hidden">
            <div class="flex min-h-[78px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 px-[60px] max-1180:px-8">
                <!-- Left Navigation Section -->
                <div class="flex items-center gap-x-10 max-[1180px]:gap-x-5">
                    <!-- Logo Shimmer -->
                    <span
                        class="shimmer block h-[29px] w-[131px] rounded"
                        role="presentation"
                    >
                    </span>

                    <!-- Categories Shimmer -->
                    <div class="flex items-center gap-5">
                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>

                        <span
                            class="shimmer h-6 w-20 rounded"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>

                <!-- Right Navigation Section -->
                <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                    <!-- Search Bar Shimmer -->
                    <div class="relative w-full max-w-[445px]">
                        <span
                            class="shimmer block h-[42px] w-[250px] rounded-lg px-11 py-3"
                            role="presentation"
                        >
                        </span>
                    </div>

                    <!-- Right Navigation Icons Shimmer -->
                    <div class="mt-1.5 flex gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">
                        <!-- Compare Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>

                        <!-- Cart Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>

                        <!-- Profile Icon Shimmer -->
                        <span
                            class="shimmer h-6 w-6 rounded"
                            role="presentation"
                        >
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Header Shimmer -->
        <div class="flex flex-wrap gap-4 px-4 pb-4 pt-6 shadow-sm lg:hidden">
            <div class="flex w-full items-center justify-between">
                <!-- Left Navigation -->
                <div class="flex items-center gap-x-1.5">
                    <!-- Hamburger Menu Shimmer -->
                    <span
                        class="shimmer block h-6 w-6 rounded"
                        role="presentation"
                    >
                    </span>

                    <!-- Logo Shimmer -->
                    <span
                        class="shimmer block h-[29px] w-[131px] rounded"
                        role="presentation"
                    >
                    </span>
                </div>

                <!-- Right Navigation Icons -->
                <div class="flex items-center gap-x-5 max-md:gap-x-4">
                    <!-- Compare Icon Shimmer -->
                    <span
                        class="shimmer block h-6 w-6 rounded"
                        role="presentation"
                    >
                    </span>

                    <!-- Cart Icon Shimmer -->
                    <span
                        class="shimmer block h-6 w-6 rounded"
                        role="presentation"
                    >
                    </span>

                    <!-- Profile Icon Shimmer -->
                    <span
                        class="shimmer block h-6 w-6 rounded"
                        role="presentation"
                    >
                    </span>
                </div>
            </div>

            <!-- Search Bar Shimmer -->
            <div class="flex w-full items-center">
                <div class="relative w-full">
                    <span
                        class="shimmer block h-[42px] w-full rounded-xl px-11 py-3.5 max-md:rounded-lg"
                        role="presentation"
                    >
                    </span>
                </div>
            </div>
        </div>
    </v-header-switcher>

    <!-- Custom navigation row -->
    <div
        id="rnjNavBar"
        style="background-color:#F1F1F1;border-top:1px solid #e4e4e7;"
    >
        <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:8px 24px;max-width:1600px;margin-left:auto;margin-right:auto;padding:0 60px;">
            <!-- Custom links -->
            <nav style="display:flex;flex-wrap:wrap;align-items:center;gap:32px;">
                @foreach ($headerNavLinks as $link)
                    <a
                        href="{{ $link['url'] }}"
                        style="{{ $sNavLink }}"
                    >
                        {{ $link['title'] }}
                    </a>
                @endforeach
            </nav>

            <!-- Live cart total -->
            <v-header-cart-total></v-header-cart-total>
        </div>
    </div>
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-header-cart-total-template"
    >
        <a
            :href="cartUrl"
            style="display:inline-flex;align-items:center;gap:9px;padding:10px 0;text-decoration:none;"
        >
            <svg
                xmlns="http://www.w3.org/2000/svg"
                width="21"
                height="21"
                viewBox="0 0 24 24"
                fill="none"
                stroke="#F4511E"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
                style="display:block;width:21px;height:21px;min-width:21px;flex:0 0 21px;"
            >
                <circle cx="9" cy="21" r="1" />
                <circle cx="20" cy="21" r="1" />
                <path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6" />
            </svg>

            <span style="font-size:14px;font-weight:700;color:#F4511E;">
                @{{ total }}
            </span>

            <span style="font-size:13px;color:#71717a;">
                (@{{ count }} @{{ count === 1 ? 'Item' : 'Items' }})
            </span>
        </a>
    </script>

    <script type="module">
        app.component('v-header-cart-total', {
            template: '#v-header-cart-total-template',

            data() {
                return {
                    cartUrl: "{{ route('shop.checkout.cart.index') }}",
                    total: "{{ core()->formatPrice(0) }}",
                    count: 0,
                };
            },

            mounted() {
                this.fetchCart();

                /**
                 * The mini-cart broadcasts this whenever the cart changes, so the
                 * total stays in sync without a page reload.
                 */
                this.$emitter.on('update-mini-cart', (cart) => this.apply(cart));
            },

            methods: {
                fetchCart() {
                    this.$axios.get("{{ route('shop.api.checkout.cart.index') }}")
                        .then(response => this.apply(response.data.data))
                        .catch(() => {});
                },

                apply(cart) {
                    if (! cart) {
                        this.total = "{{ core()->formatPrice(0) }}";
                        this.count = 0;

                        return;
                    }

                    this.total = cart.formatted_grand_total ?? this.total;
                    this.count = cart.items_count ?? 0;
                },
            },
        });
    </script>

    <script
        type="text/x-template"
        id="v-header-switcher-template"
    >
        <v-desktop-header v-if="isDesktop"></v-desktop-header>

        <v-mobile-header v-else></v-mobile-header>
    </script>

    <script type="module">
        app.component('v-header-switcher', {
            template: '#v-header-switcher-template',

            data() {
                return {
                    isDesktop: window.innerWidth >= 1024
                }
            },

            mounted() {
                this.media = window.matchMedia('(min-width: 1024px)');

                this.media.addEventListener('change', this.handleMedia);
            },

            beforeUnmount() {
                this.media.removeEventListener('change', this.handleMedia);
            },

            methods: {
                handleMedia(e) {
                    this.isDesktop = e.matches;
                }
            }
        });

        app.component('v-desktop-header', {
            template: '#v-desktop-header-template'
        });

        app.component('v-mobile-header', {
            template: '#v-mobile-header-template'
        });
    </script>

    <script
        type="text/x-template"
        id="v-desktop-header-template"
    >
        <x-shop::layouts.header.desktop />
    </script>

    <script
        type="text/x-template"
        id="v-mobile-header-template"
    >
        <x-shop::layouts.header.mobile />
    </script>
@endPushOnce
