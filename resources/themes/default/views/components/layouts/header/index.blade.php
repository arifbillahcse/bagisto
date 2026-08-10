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

    /**
     * The utility bar keeps its structure in inline styles so it renders
     * correctly even before the stylesheet in the `scripts` stack is parsed.
     */
    $sTopLink = 'font-size:13px;color:#ffffff;text-decoration:none;white-space:nowrap;';
@endphp

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
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}

@pushOnce('scripts')
    {{--
        These rules are emitted through the `scripts` stack because that stack
        renders AFTER the closing </div> of #app. A <style> tag placed inside
        #app is parsed by Vue as part of the root template and never reaches the
        document, which is why the header rows rendered unstyled.
    --}}
    <style>
    #rnjTopBar a:hover {
        opacity: .8;
    }

    @media (max-width: 1023px) {
        #rnjTopBar {
            display: none !important;
        }
    }

    /* ===== Desktop header: main row ===== */
    .rnj-mainrow {
        display: flex;
        align-items: center;
        gap: 40px;
        width: 100%;
        min-height: 84px;
        padding: 12px 60px;
        background-color: #ffffff;
    }

    .rnj-mainrow__logo {
        flex: 0 0 auto;
    }

    .rnj-mainrow__logo img {
        display: block;
        width: auto;
        height: auto;
        max-height: 46px;
    }

    .rnj-mainrow__search {
        flex: 1 1 auto;
        min-width: 0;
    }

    .rnj-search {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 640px;
        margin-inline: auto;
    }

    .rnj-search__icon {
        position: absolute;
        left: 14px;
        display: flex;
        align-items: center;
        font-size: 20px;
        color: #71717a;
        pointer-events: none;
    }

    html[dir="rtl"] .rnj-search__icon {
        left: auto;
        right: 14px;
    }

    .rnj-search__input {
        width: 100%;
        padding: 13px 44px;
        font-size: 14px;
        color: #18181b;
        background-color: #f4f4f5;
        border: 1px solid transparent;
        border-radius: 8px;
        transition: border-color .15s ease;
    }

    .rnj-search__input:hover,
    .rnj-search__input:focus {
        border-color: #a1a1aa;
        outline: none;
    }

    /* Right-hand actions */
    .rnj-actions {
        display: flex;
        align-items: flex-start;
        flex: 0 0 auto;
        gap: 26px;
    }

    .rnj-action {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 3px;
        cursor: pointer;
        text-decoration: none;
        color: #18181b;
    }

    .rnj-action__icon {
        display: inline-block;
        font-size: 22px;
        line-height: 1;
    }

    .rnj-action__label {
        font-size: 12px;
        font-weight: 500;
        line-height: 1.2;
        white-space: nowrap;
        color: #3f3f46;
    }

    .rnj-action:hover .rnj-action__label,
    .rnj-action:hover .rnj-action__icon {
        color: #F4511E;
    }

    /* The mini-cart renders its own icon + badge; align it like the others */
    .rnj-action--cart .icon-cart {
        font-size: 22px;
        line-height: 1;
    }

    @media (max-width: 1180px) {
        .rnj-mainrow {
            gap: 24px;
            padding: 12px 32px;
        }

        .rnj-actions {
            gap: 18px;
        }
    }

    /* ===== Desktop header: category bar ===== */
    .rnj-catbar {
        width: 100%;
        background-color: #0D2818;
    }

    .rnj-catbar__inner {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 4px 8px;
        max-width: 1600px;
        margin-inline: auto;
        padding: 0 60px;
    }

    .rnj-catbar__item {
        position: relative;
    }

    .rnj-catbar__link {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 14px 12px;
        font-size: 14px;
        font-weight: 500;
        color: #ffffff;
        text-decoration: none;
        white-space: nowrap;
        transition: color .15s ease;
    }

    .rnj-catbar__item:hover .rnj-catbar__link {
        color: #F4511E;
    }

    .rnj-catbar__caret {
        font-size: 16px;
        line-height: 1;
    }

    /* Sub-category flyout */
    .rnj-catbar__panel {
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 20;
        min-width: 240px;
        max-width: 340px;
        max-height: 70vh;
        overflow-y: auto;
        padding: 8px 0;
        background-color: #ffffff;
        border-top: 3px solid #F4511E;
        box-shadow: 0 8px 20px rgba(0, 0, 0, .18);
        opacity: 0;
        visibility: hidden;
        transform: translateY(6px);
        transition: opacity .18s ease, transform .18s ease, visibility .18s;
    }

    html[dir="rtl"] .rnj-catbar__panel {
        left: auto;
        right: 0;
    }

    .rnj-catbar__item:hover .rnj-catbar__panel {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    /* Sub-categories stack vertically, one per line, under their parent. */
    .rnj-catbar__columns {
        display: block;
    }

    .rnj-catbar__child {
        display: block;
        padding: 9px 22px;
        font-size: 14px;
        font-weight: 500;
        color: #0B2540;
        text-decoration: none;
    }

    .rnj-catbar__child:hover {
        color: #F4511E;
        background-color: #f7f7f8;
    }

    .rnj-catbar__grandchildren {
        list-style: none;
        margin: 0;
        padding: 0 0 4px;
    }

    .rnj-catbar__grandchild {
        display: block;
        padding: 6px 22px 6px 36px;
        font-size: 13px;
        color: #52525b;
        text-decoration: none;
    }

    .rnj-catbar__grandchild:hover {
        color: #F4511E;
        background-color: #f7f7f8;
    }

    @media (max-width: 1180px) {
        .rnj-catbar__inner {
            padding: 0 32px;
        }

        .rnj-catbar__link {
            padding: 12px 8px;
            font-size: 13px;
        }
    }
    </style>

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
