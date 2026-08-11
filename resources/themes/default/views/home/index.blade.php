@php
    $channel = core()->getCurrentChannel();
@endphp

<!-- SEO Meta Content -->
@push ('meta')
    <meta
        name="title"
        content="{{ $channel->home_seo['meta_title'] ?? '' }}"
    />

    <meta
        name="description"
        content="{{ $channel->home_seo['meta_description'] ?? '' }}"
    />

    <meta
        name="keywords"
        content="{{ $channel->home_seo['meta_keywords'] ?? '' }}"
    />
@endPush

@push('scripts')
    @if(! empty($categories))
        <script>
            localStorage.setItem('categories', JSON.stringify(@json($categories)));
        </script>
    @endif
@endpush

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-hero-slider-template"
    >
        <div>
            <div
                class="tz-hero2__track"
                :style="{ transform: 'translateX(-' + (currentIndex * 100) + '%)' }"
            >
                <a
                    class="tz-hero2__slide tz-hero2__link"
                    v-for="(slide, index) in slides"
                    :key="index"
                    :href="slide.link || '#'"
                >
                    <img
                        class="tz-hero2__img"
                        :src="slide.image"
                        :alt="slide.title || ('Slide ' + (index + 1))"
                        :loading="index === 0 ? 'eager' : 'lazy'"
                    />
                </a>
            </div>

            <template v-if="slides.length > 1">
                <button
                    type="button"
                    class="tz-hero2__nav tz-hero2__nav--prev"
                    aria-label="Previous slide"
                    @click.prevent="go(currentIndex - 1)"
                >&#8592;</button>

                <button
                    type="button"
                    class="tz-hero2__nav tz-hero2__nav--next"
                    aria-label="Next slide"
                    @click.prevent="go(currentIndex + 1)"
                >&#8594;</button>

                <div class="tz-hero2__dots">
                    <button
                        type="button"
                        class="tz-hero2__dot"
                        :class="{ 'tz-hero2__dot--active': index === currentIndex }"
                        v-for="(slide, index) in slides"
                        :key="index"
                        :aria-label="'Go to slide ' + (index + 1)"
                        @click.prevent="go(index)"
                    ></button>
                </div>
            </template>
        </div>
    </script>

    <script type="module">
        app.component('v-hero-slider', {
            template: '#v-hero-slider-template',

            props: ['slides'],

            data() {
                return {
                    currentIndex: 0,
                    timer: null,
                };
            },

            mounted() {
                this.play();
            },

            beforeUnmount() {
                clearInterval(this.timer);
            },

            methods: {
                go(index) {
                    const total = this.slides.length;

                    this.currentIndex = (index + total) % total;

                    this.play();
                },

                play() {
                    clearInterval(this.timer);

                    if (this.slides.length < 2) {
                        return;
                    }

                    this.timer = setInterval(() => {
                        this.currentIndex = (this.currentIndex + 1) % this.slides.length;
                    }, 5000);
                },
            },
        });
    </script>
@endPushOnce

{{--
    All homepage styles are scoped under the .tz- prefix and pushed to the
    layout's @stack('styles') slot (renders after the compiled app.css).
    Plain CSS only — no Tailwind utilities — so no Vite rebuild is needed
    and nothing can collide with global theme classes.
--}}
@pushOnce('styles')
    <style>
        :root {
            --tz-accent: #29ABE2;
            --tz-accent-dark: #1690c4;
            --tz-navy: #0B2540;
            --tz-text: #52525b;
            --tz-muted: #71717a;
            --tz-light: #f5f7f9;
            --tz-border: #e4e4e7;
        }

        .tz-container {
            max-width: 1400px;
            margin-inline: auto;
            padding-inline: 60px;
        }

        .tz-section-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--tz-navy);
            display: flex;
            align-items: center;
            gap: 20px;
            margin-bottom: 28px;
        }

        .tz-section-title::after {
            content: "";
            flex: 1;
            height: 1px;
            background: var(--tz-border);
        }

        .tz-catsection {
            padding: 56px 0;
        }

        .tz-catsection .tz-section-title {
            margin-bottom: 0;
        }

        .tz-btn {
            display: inline-block;
            background: var(--tz-accent);
            color: #ffffff;
            font-size: .9rem;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
            transition: background .15s ease;
        }

        .tz-btn:hover {
            background: var(--tz-accent-dark);
            color: #ffffff;
        }

        /* ============ 1. HERO: slider + fixed banner ============ */
        .tz-hero2 {
            background: linear-gradient(180deg, #fbfcfd 0%, #f2f6f9 100%);
            padding: 24px 0 32px;
        }

        .tz-hero2__grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            align-items: stretch;
        }

        .tz-hero2__panel {
            position: relative;
            overflow: hidden;
            border-radius: 14px;
            background-color: #ffffff;
        }

        .tz-hero2__link {
            display: block;
            height: 100%;
        }

        .tz-hero2__img {
            display: block;
            width: 100%;
            height: 100%;
            object-fit: cover;
            aspect-ratio: 2.4 / 1;
        }

        /* The banner column is half the width, so it needs a taller ratio to match */
        .tz-hero2__panel:last-child .tz-hero2__img {
            aspect-ratio: 1.18 / 1;
        }

        .tz-hero2__empty {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            min-height: 240px;
            padding: 28px;
            text-align: center;
            font-size: .875rem;
            line-height: 1.6;
            color: var(--tz-muted);
            border: 1px dashed var(--tz-border);
            border-radius: 14px;
        }

        /* Slider chrome */
        .tz-hero2__track {
            display: flex;
            transition: transform .6s ease-out;
            will-change: transform;
        }

        .tz-hero2__slide {
            flex: 0 0 100%;
            width: 100%;
        }

        .tz-hero2__nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, .9);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
            color: #18181b;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            opacity: 0;
            transition: opacity .2s ease;
        }

        .tz-hero2__panel:hover .tz-hero2__nav {
            opacity: 1;
        }

        .tz-hero2__nav--prev {
            left: 12px;
        }

        .tz-hero2__nav--next {
            right: 12px;
        }

        .tz-hero2__dots {
            position: absolute;
            bottom: 14px;
            left: 0;
            display: flex;
            justify-content: center;
            gap: 7px;
            width: 100%;
        }

        .tz-hero2__dot {
            width: 9px;
            height: 9px;
            padding: 0;
            border: 0;
            border-radius: 50%;
            background-color: rgba(255, 255, 255, .55);
            cursor: pointer;
            transition: background-color .2s ease;
        }

        .tz-hero2__dot--active {
            background-color: #ffffff;
        }

        @media (max-width: 900px) {
            .tz-hero2__grid {
                grid-template-columns: 1fr;
            }

            .tz-hero2__panel:last-child .tz-hero2__img {
                aspect-ratio: 2.4 / 1;
            }
        }

        /* ============ 1b. LEGACY HERO (unused, kept for reference) ============ */
        .tz-hero {
            background:
                linear-gradient(180deg, #fbfcfd 0%, #f2f6f9 100%);
            padding: 40px 0 48px;
        }

        .tz-hero__inner {
            display: grid;
            grid-template-columns: 250px 1fr 1fr;
            gap: 40px;
            align-items: start;
        }

        .tz-hero__categories {
            background: #ffffff;
            border: 1px solid var(--tz-border);
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(11, 37, 64, .05);
        }

        .tz-hero__categories-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--tz-navy);
            margin-bottom: 14px;
        }

        .tz-hero__categories ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .tz-hero__categories li {
            border-bottom: 1px solid #eef1f4;
        }

        .tz-hero__categories li:last-child {
            border-bottom: none;
        }

        .tz-hero__categories a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 2px;
            font-size: .875rem;
            color: var(--tz-text);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .tz-hero__categories a:hover {
            color: var(--tz-accent);
        }

        .tz-hero__categories a::before {
            content: "\2139";
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            flex-shrink: 0;
            border: 1.5px solid var(--tz-accent);
            border-radius: 50%;
            color: var(--tz-accent);
            font-size: .7rem;
            font-weight: 700;
            text-decoration: none;
        }

        .tz-hero__content {
            align-self: center;
            padding-top: 30px;
        }

        .tz-hero__eyebrow {
            color: var(--tz-accent);
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 14px;
        }

        .tz-hero__heading {
            font-size: 2.6rem;
            line-height: 1.2;
            font-weight: 800;
            color: #17262f;
            margin-bottom: 30px;
            max-width: 480px;
        }

        .tz-hero__media {
            align-self: center;
            display: flex;
            justify-content: center;
        }

        .tz-hero__media-circle {
            width: 360px;
            height: 360px;
            max-width: 100%;
            border-radius: 50%;
            background: radial-gradient(circle at 35% 30%, #b99a83, #8f7261);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .tz-hero__media-circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .tz-hero__media-placeholder {
            color: rgba(255, 255, 255, .85);
            font-size: .8rem;
            text-align: center;
            padding: 20px;
        }

        /* ============ 2. BRAND STRIP ============ */
        .tz-brands {
            padding: 44px 0;
            background: #ffffff;
        }

        .tz-brands__inner {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 30px 40px;
        }

        .tz-brands__item {
            display: inline-flex;
            align-items: center;
            opacity: .75;
            filter: grayscale(1);
            transition: opacity .15s ease, filter .15s ease;
        }

        .tz-brands__item:hover {
            opacity: 1;
            filter: grayscale(0);
        }

        .tz-brands__item img {
            max-height: 40px;
            width: auto;
        }

        /* ============ 3b. CATEGORY BANNER ============ */
        .tz-catbanner {
            padding: 28px 0;
            background: #ffffff;
        }

        .tz-catbanner__link {
            display: block;
            border-radius: 10px;
            overflow: hidden;
        }

        .tz-catbanner__img {
            display: block;
            width: 100%;
            height: auto;
        }

        /* ============ 5. PROMO BANNER ============ */
        .tz-promo {
            margin-top: 64px;
            background: #76C043;
            padding: 60px 0 70px;
        }

        .tz-promo__head {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            align-items: center;
            margin-bottom: 44px;
        }

        .tz-promo__heading {
            font-size: 2.2rem;
            font-weight: 800;
            line-height: 1.25;
            color: #ffffff;
        }

        .tz-promo__text {
            color: #f0fafe;
            font-size: .95rem;
            line-height: 1.65;
            margin-bottom: 22px;
        }

        .tz-promo__btn {
            display: inline-block;
            background: #0c5e70;
            color: #ffffff;
            font-size: .9rem;
            font-weight: 600;
            padding: 12px 28px;
            border-radius: 6px;
            text-decoration: none;
        }

        .tz-promo__btn:hover {
            background: #0a4d5c;
            color: #ffffff;
        }

        .tz-promo__cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .tz-promo__card {
            background: #ffffff;
            border-radius: 12px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
            box-shadow: 0 8px 24px rgba(11, 37, 64, .12);
        }

        .tz-promo__card-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: var(--tz-navy);
        }

        .tz-promo__card-text {
            font-size: .85rem;
            color: var(--tz-muted);
            line-height: 1.5;
        }

        .tz-promo__card-img {
            width: 100%;
            height: 120px;
            border-radius: 8px;
            background: var(--tz-light);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--tz-muted);
            font-size: .8rem;
            overflow: hidden;
        }

        .tz-promo__card-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* ============ 6. FEATURE STRIP (used beside/below sections) ============ */
        .tz-features {
            background: var(--tz-accent);
            margin-top: 72px;
            padding: 30px 0;
        }

        .tz-features__inner {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
        }

        .tz-feature {
            display: flex;
            gap: 14px;
            align-items: flex-start;
            color: #ffffff;
        }

        .tz-feature + .tz-feature {
            border-inline-start: 1px solid rgba(255, 255, 255, .35);
            padding-inline-start: 28px;
        }

        .tz-feature__icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .tz-feature__icon svg {
            width: 34px;
            height: 34px;
            stroke: #ffffff;
        }

        .tz-feature__title {
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .tz-feature__text {
            font-size: .8rem;
            line-height: 1.5;
            color: #f0fafe;
        }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 1100px) {
            .tz-container {
                padding-inline: 32px;
            }

            .tz-hero__inner {
                grid-template-columns: 1fr 1fr;
            }

            .tz-hero__categories {
                display: none;
            }

            .tz-promo__head,
            .tz-promo__cards {
                grid-template-columns: 1fr;
            }

            .tz-features__inner {
                grid-template-columns: 1fr 1fr;
            }

            .tz-feature:nth-child(3) {
                border-inline-start: none;
                padding-inline-start: 0;
            }
        }

        @media (max-width: 640px) {
            .tz-container {
                padding-inline: 16px;
            }

            .tz-hero__inner {
                grid-template-columns: 1fr;
            }

            .tz-hero__heading {
                font-size: 1.9rem;
            }

            .tz-hero__media-circle {
                width: 260px;
                height: 260px;
            }

            .tz-catsection {
                padding: 36px 0;
            }

            .tz-brands__inner {
                justify-content: center;
            }

            .tz-brands__item {
                font-size: 1.3rem;
            }

            .tz-features__inner {
                grid-template-columns: 1fr;
            }

            .tz-feature + .tz-feature {
                border-inline-start: none;
                padding-inline-start: 0;
                border-top: 1px solid rgba(255, 255, 255, .35);
                padding-top: 20px;
            }

            .tz-promo__heading,
            .tz-faq__heading,
            .tz-disclaimer__heading {
                font-size: 1.5rem;
            }
        }
    </style>
@endPushOnce

{{-- has-feature="false" hides Bagisto's built-in services strip — the page has its own .tz-features strip --}}
<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{  $channel->home_seo['meta_title'] ?? '' }}
    </x-slot>

    {!! view_render_event('bagisto.shop.home.content.before') !!}

    <!-- ============ 1. HERO: slider + fixed offer banner ============ -->
    @php
        /**
         * Both panels are driven by `image_carousel` theme customizations matched
         * on their NAME, so slides and the banner are managed from
         * Admin -> Settings -> Themes -> Create Theme (Type: Image Carousel):
         *
         *   "Hero Slider"  - every image becomes a rotating slide on the left.
         *   "Offer Banner" - the FIRST image is shown as the static right panel.
         *
         * Bagisto's own <x-shop::carousel> is not reused here because it sizes
         * slides from window.innerWidth, which breaks inside a half-width column.
         */
        $heroCarousels = collect($customizations)->where('type', 'image_carousel');

        $heroSliderRecord = $heroCarousels->firstWhere('name', 'Hero Slider')
            ?? $heroCarousels->first();

        $heroSlides = collect($heroSliderRecord?->options['images'] ?? [])
            ->filter(fn ($slide) => ! empty($slide['image']))
            ->values();

        $heroBanner = collect($heroCarousels->firstWhere('name', 'Offer Banner')?->options['images'] ?? [])
            ->firstWhere('image', '!=', null);
    @endphp

    <section class="tz-hero2">
        <div class="tz-container">
            <div class="tz-hero2__grid">
                <!-- Rotating slider -->
                <div class="tz-hero2__panel">
                    @if ($heroSlides->isNotEmpty())
                        <v-hero-slider :slides="{{ json_encode($heroSlides->values()) }}">
                            {{-- Server-rendered first slide so it paints before Vue mounts --}}
                            <a
                                href="{{ ($heroSlides->first()['link'] ?? '') ?: '#' }}"
                                class="tz-hero2__link"
                            >
                                <img
                                    src="{{ $heroSlides->first()['image'] }}"
                                    alt="{{ $heroSlides->first()['title'] ?? 'Offer' }}"
                                    class="tz-hero2__img"
                                    fetchpriority="high"
                                />
                            </a>
                        </v-hero-slider>
                    @else
                        <div class="tz-hero2__empty">
                            Create an <strong>Image Carousel</strong> named <strong>“Hero Slider”</strong> in the admin panel to show slides here.
                        </div>
                    @endif
                </div>

                <!-- Fixed offer banner -->
                <div class="tz-hero2__panel">
                    @if ($heroBanner)
                        <a
                            href="{{ ($heroBanner['link'] ?? '') ?: '#' }}"
                            class="tz-hero2__link"
                        >
                            <img
                                src="{{ $heroBanner['image'] }}"
                                alt="{{ $heroBanner['title'] ?? 'Offer' }}"
                                class="tz-hero2__img"
                            />
                        </a>
                    @else
                        <div class="tz-hero2__empty">
                            Create an <strong>Image Carousel</strong> named <strong>“Offer Banner”</strong> in the admin panel to show a banner here.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{--
        Flash Sale - products are hand-picked in
        Admin -> Settings -> Themes -> Create Theme (Type: Flash Sale).

        Rendered with the stock product carousel (same component as the
        sections below), which fetches the hand-picked products from a
        dedicated endpoint. Keeping it on the native component means the
        cards, arrows and responsive behaviour stay identical to the rest
        of the page and need no theme-local Tailwind classes -- this file
        is outside the Shop package's Tailwind content globs, so utility
        classes written here would not be compiled.
    --}}
    @php
        $flashSaleOptions = collect($customizations)->firstWhere('type', 'flash_sale')?->options ?? [];
    @endphp

    @if (! empty($flashSaleOptions['product_ids']))
        <x-shop::products.carousel
            :title="$flashSaleOptions['title'] ?? 'Flash Sale'"
            :src="route('shop.api.products.flash_sale.index')"
            :navigation-link="$flashSaleOptions['view_all_url'] ?? ''"
            aria-label="Flash sale products"
        />
    @endif

    {{--
        Shop by Category - the native `x-shop::categories.carousel` component
        accepts a `title` prop but never renders it, so the heading (and the
        extra breathing room around the section) is added here instead.

        The heading is optional and admin-editable: create a `Footer Links`
        theme customization named "Category Section Heading" with a single
        link whose Title is the heading text (its URL is unused). Falls back
        to "Shop by Category" if that entry doesn't exist.
    --}}
    @php
        $categoryHeadingRecord = collect($customizations)->where('type', 'footer_links')->firstWhere('name', 'Category Section Heading');

        $categoryHeading = collect($categoryHeadingRecord?->options ?? [])
            ->flatMap(fn ($column) => $column)
            ->first()['title'] ?? 'Shop by Category';
    @endphp

    <!-- ============ 3. SHOP BY CATEGORY (native, real data) ============ -->
    <section class="tz-catsection">
        <div class="tz-container">
            <h2 class="tz-section-title">{{ $categoryHeading }}</h2>
        </div>

        <x-shop::categories.carousel
            :title="$categoryHeading"
            :src="route('shop.api.categories.index', ['parent_id' => $channel->root_category_id, 'sort' => 'asc', 'limit' => 10])"
            :navigation-link="route('shop.home.index')"
            aria-label="Shop by category"
        />
    </section>

    <!-- ============ 3b. CATEGORY BANNER ============ -->
    @php
        /**
         * Driven by an `image_carousel` theme customization named "Category Banner"
         * (Admin -> Settings -> Themes -> Create Theme -> Type: Image Carousel).
         * Only the FIRST uploaded image is used as a single, full-width banner.
         */
        $categoryBannerImage = collect($customizations)
            ->where('type', 'image_carousel')
            ->firstWhere('name', 'Category Banner')
            ?->options['images'] ?? [];

        $categoryBannerImage = collect($categoryBannerImage)->firstWhere('image', '!=', null);
    @endphp

    @if ($categoryBannerImage)
        <section class="tz-catbanner">
            <div class="tz-container">
                <a
                    href="{{ ($categoryBannerImage['link'] ?? '') ?: '#' }}"
                    class="tz-catbanner__link"
                >
                    <img
                        src="{{ $categoryBannerImage['image'] }}"
                        alt="{{ $categoryBannerImage['title'] ?? 'Banner' }}"
                        class="tz-catbanner__img"
                        loading="lazy"
                    />
                </a>
            </div>
        </section>
    @endif

    <!-- ============ 4. POPULAR PRODUCTS (native, real data) ============ -->
    <x-shop::products.carousel
        title="Our Popular Products"
        :src="route('shop.api.products.index', ['featured' => 1, 'sort' => 'created_at-desc', 'limit' => 12])"
        :navigation-link="route('shop.search.index', ['featured' => 1])"
        aria-label="Popular products"
    />

    {{--
        Promo Banner - admin-editable in Admin -> Settings -> Themes ->
        Create Theme (Type: Promo Banner). Heading/text/button plus a
        free-form list of cards (title, text, optional image, optional
        button) are stored as JSON options and rendered here.
    --}}
    @php
        $promoBanner = collect($customizations)->firstWhere('type', 'promo_banner')?->options ?? [];
        $promoCards = collect($promoBanner['cards'] ?? [])->filter(fn ($card) => ! empty($card['title']))->values();
    @endphp

    @if (! empty($promoBanner['heading']) || $promoCards->isNotEmpty())
        <section class="tz-promo">
            <div class="tz-container">
                @if (! empty($promoBanner['heading']))
                    <div class="tz-promo__head">
                        <div></div>

                        <div>
                            <h2 class="tz-promo__heading">
                                {{ $promoBanner['heading'] }}
                            </h2>

                            @if (! empty($promoBanner['text']))
                                <p class="tz-promo__text">
                                    {{ $promoBanner['text'] }}
                                </p>
                            @endif

                            @if (! empty($promoBanner['button_text']))
                                <a
                                    href="{{ $promoBanner['button_link'] ?? route('shop.search.index') }}"
                                    class="tz-promo__btn"
                                >
                                    {{ $promoBanner['button_text'] }}
                                </a>
                            @endif
                        </div>
                    </div>
                @endif

                @if ($promoCards->isNotEmpty())
                    <div class="tz-promo__cards">
                        @foreach ($promoCards as $card)
                            <div class="tz-promo__card">
                                <p class="tz-promo__card-title">{{ $card['title'] }}</p>

                                @if (! empty($card['text']))
                                    <p class="tz-promo__card-text">{{ $card['text'] }}</p>
                                @endif

                                @if (! empty($card['image']))
                                    <div class="tz-promo__card-img">
                                        <img
                                            src="{{ $card['image'] }}"
                                            alt="{{ $card['title'] }}"
                                        />
                                    </div>
                                @endif

                                @if (! empty($card['button_text']))
                                    <a
                                        href="{{ $card['button_link'] ?? route('shop.search.index') }}"
                                        class="tz-btn"
                                    >
                                        {{ $card['button_text'] }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </section>
    @endif

    <!-- ============ 6. NEW ARRIVAL (native, real data) ============ -->
    <x-shop::products.carousel
        title="New Arrival"
        :src="route('shop.api.products.index', ['new' => 1, 'sort' => 'created_at-desc', 'limit' => 12])"
        :navigation-link="route('shop.search.index', ['new' => 1])"
        aria-label="New arrivals"
    />

    <!-- ============ 7. JUST FOR YOU (personalized grid: 3×4 = 12 products) ============ -->
    <x-shopext::just-for-you-grid
        title="Just for You"
        :src="route('shop.api.products.just_for_you.index', ['limit' => 12])"
        :navigation-link="route('shop.search.index')"
        aria-label="Just for you"
    />

    {{--
        Driven by an `image_carousel` theme customization named "Brands"
        (Admin -> Settings -> Themes -> Create Theme, Type: Image Carousel).
        Each slide's image becomes a logo; its link is where the logo sends
        the customer -- point it at a brand-filtered catalog search URL,
        e.g. {{ route('shop.search.index') }}?brand=Nike

        The section heading is optional and admin-editable too: create a
        `Footer Links` theme customization named "Brand Strip Heading" with
        a single link whose Title is the heading text (its URL is unused).
        Falls back to "Shop by Brand" if that entry doesn't exist.
    --}}
    @php
        $brandRecord = collect($customizations)->where('type', 'image_carousel')->firstWhere('name', 'Brands');

        $brandLogos = collect($brandRecord?->options['images'] ?? [])
            ->filter(fn ($logo) => ! empty($logo['image']))
            ->values();

        $brandHeadingRecord = collect($customizations)->where('type', 'footer_links')->firstWhere('name', 'Brand Strip Heading');

        $brandHeading = collect($brandHeadingRecord?->options ?? [])
            ->flatMap(fn ($column) => $column)
            ->first()['title'] ?? 'Shop by Brand';
    @endphp

    <!-- ============ 7a. BRAND STRIP ============ -->
    <section class="tz-brands">
        <div class="tz-container">
            @if ($brandLogos->isNotEmpty())
                <h2 class="tz-section-title">{{ $brandHeading }}</h2>

                <div class="tz-brands__inner">
                    @foreach ($brandLogos as $logo)
                        <a
                            href="{{ $logo['link'] ?: '#' }}"
                            class="tz-brands__item"
                        >
                            <img
                                src="{{ $logo['image'] }}"
                                alt="{{ $logo['title'] ?? 'Brand' }}"
                                loading="lazy"
                            >
                        </a>
                    @endforeach
                </div>
            @else
                <div class="tz-hero2__empty">
                    Create an <strong>Image Carousel</strong> named <strong>“Brands”</strong> in the admin panel to show brand logos here.
                </div>
            @endif
        </div>
    </section>


    <!-- ============ 12. SERVICES STRIP ============ -->
    <section class="tz-features">
        <div class="tz-container">
            <div class="tz-features__inner">
                <div class="tz-feature">
                    <span class="tz-feature__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" /></svg>
                    </span>

                    <div>
                        <p class="tz-feature__title">Free Shipping</p>
                        <p class="tz-feature__text">Free shipping on qualifying orders.</p>
                    </div>
                </div>

                <div class="tz-feature">
                    <span class="tz-feature__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                    </span>

                    <div>
                        <p class="tz-feature__title">Online Payment</p>
                        <p class="tz-feature__text">Pay by cash on delivery or any major card.</p>
                    </div>
                </div>

                <div class="tz-feature">
                    <span class="tz-feature__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15.59 14.37a6 6 0 01-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 006.16-12.12A14.98 14.98 0 009.631 8.41m5.96 5.96a14.926 14.926 0 01-5.841 2.58m-.119-8.54a6 6 0 00-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 00-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 01-2.448-2.448 14.9 14.9 0 01.06-.312m-2.24 2.39a4.493 4.493 0 00-1.757 4.306 4.493 4.493 0 004.306-1.758M16.5 9a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                    </span>

                    <div>
                        <p class="tz-feature__title">Fast Delivery</p>
                        <p class="tz-feature__text">Quick delivery straight to your door.</p>
                    </div>
                </div>

                <div class="tz-feature">
                    <span class="tz-feature__icon">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636a9 9 0 010 12.728m-12.728 0a9 9 0 010-12.728m9.9 2.829a5 5 0 010 7.07m-7.072 0a5 5 0 010-7.07M13.5 12a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" /></svg>
                    </span>

                    <div>
                        <p class="tz-feature__title">24/7 Support</p>
                        <p class="tz-feature__text">Available around the clock for inquiries.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {!! view_render_event('bagisto.shop.home.content.after') !!}
</x-shop::layouts>
