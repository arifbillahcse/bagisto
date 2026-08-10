{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    Repositories are injected directly because an anonymous Blade component
    cannot receive data from a view composer.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')
@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $channel = core()->getCurrentChannel();

    /**
     * Admin-managed link sections (Admin -> Settings -> Themes -> Footer Links).
     * Each section is a list of ['title' => ..., 'url' => ..., 'sort_order' => ...].
     *
     * The header rows are driven by footer_links records too, matched on their
     * name, so those names are skipped here - otherwise the footer would render
     * whichever record happened to be created first.
     */
    $reservedForHeader = ['Top Bar Contact', 'Top Bar Links', 'Header Links', 'Chat Widgets'];

    $linkCustomization = $themeCustomizationRepository->findWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ])->reject(fn ($record) => in_array($record->name, $reservedForHeader, true))->first();

    /**
     * Flatten the admin sections into a single "Useful Links" list. When the
     * admin has not configured any links yet, fall back to the default pages
     * that ship with Bagisto so the column is never empty.
     */
    $usefulLinks = collect($linkCustomization?->options ?? [])
        ->flatMap(fn ($section) => $section)
        ->sortBy('sort_order')
        ->map(fn ($link) => ['title' => $link['title'], 'url' => $link['url']])
        ->values();

    if ($usefulLinks->isEmpty()) {
        $usefulLinks = collect([
            ['title' => 'Home',               'url' => route('shop.home.index')],
            ['title' => 'About Us',           'url' => route('shop.cms.page', 'about-us')],
            ['title' => 'Contact Us',         'url' => route('shop.home.contact_us')],
            ['title' => 'Privacy Policy',     'url' => route('shop.cms.page', 'privacy-policy')],
            ['title' => 'Return Policy',      'url' => route('shop.cms.page', 'return-policy')],
            ['title' => 'Terms & Conditions', 'url' => route('shop.cms.page', 'terms-conditions')],
        ]);
    }

    $usefulLinks = $usefulLinks->take(6);

    /**
     * Top-level storefront categories, pulled live from the catalog.
     */
    $footerCategories = collect($categoryRepository->getVisibleCategoryTree($channel->root_category_id))->take(6);

    /**
     * Floating WhatsApp / Messenger chat buttons (Admin -> Settings -> Themes
     * -> Create Theme, Type: Footer Links, Name: "Chat Widgets"). Add a link
     * titled "WhatsApp" with a `https://wa.me/<countrycode><number>` URL
     * and/or one titled "Messenger" with a `https://m.me/<page-username>`
     * URL. Either (or both) can be left out -- only configured ones render.
     */
    $chatWidgetLinks = collect(
        $themeCustomizationRepository->findWhere([
            'type'       => 'footer_links',
            'status'     => 1,
            'theme_code' => $channel->theme,
            'channel_id' => $channel->id,
        ])->firstWhere('name', 'Chat Widgets')?->options ?? []
    )->flatMap(fn ($section) => $section);

    $whatsappLink = $chatWidgetLinks->first(fn ($link) => str_contains(strtolower($link['title'] ?? ''), 'whatsapp'));
    $messengerLink = $chatWidgetLinks->first(fn ($link) => str_contains(strtolower($link['title'] ?? ''), 'messenger'));

    /**
     * Every structural rule below is written as an inline style attribute so the
     * footer renders correctly even when no stylesheet reaches the browser.
     * The <style> block that follows only adds hover colours and responsive
     * breakpoints, which inline styles cannot express - it is pure enhancement.
     */
    $sHeading   = 'margin:0 0 20px;font-size:13px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;color:#18181b;';
    $sList      = 'list-style:none;margin:0;padding:0;display:block;';
    $sListItem  = 'margin:0 0 12px;padding:0;';
    $sLink      = 'font-size:14px;line-height:1.4;color:#71717a;text-decoration:none;';
@endphp

<style>
    /* Enhancement only - the footer is fully laid out by inline styles above. */
    #shopFooter a:hover {
        color: #060C3B !important;
    }

    /* Floating WhatsApp / Messenger chat buttons */
    .tz-chatwidgets {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    html[dir="rtl"] .tz-chatwidgets {
        right: auto;
        left: 24px;
    }

    .tz-chatwidgets__btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 54px;
        height: 54px;
        border-radius: 50%;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .25);
        transition: transform .15s ease;
    }

    .tz-chatwidgets__btn:hover {
        transform: scale(1.08);
    }

    .tz-chatwidgets__btn--whatsapp {
        background-color: #25D366;
    }

    .tz-chatwidgets__btn--messenger {
        background-color: #0084FF;
    }

    @media (max-width: 640px) {
        .tz-chatwidgets {
            bottom: 16px;
            right: 16px;
        }

        html[dir="rtl"] .tz-chatwidgets {
            left: 16px;
        }

        .tz-chatwidgets__btn {
            width: 48px;
            height: 48px;
        }
    }

    @media (max-width: 1024px) {
        #shopFooterGrid {
            grid-template-columns: 1fr 1fr !important;
            padding: 40px 32px !important;
        }

        #shopFooterBrand {
            grid-column: 1 / -1 !important;
            max-width: 100% !important;
        }
    }

    @media (max-width: 640px) {
        #shopFooterGrid {
            grid-template-columns: 1fr !important;
            padding: 28px 16px !important;
            gap: 28px !important;
        }

        #shopFooterBar {
            flex-direction: column !important;
            text-align: center !important;
            padding: 16px 20px !important;
        }
    }
</style>

<footer
    id="shopFooter"
    style="margin-top:36px;background-color:#ffffff;border-top:1px solid #e4e4e7;"
>
    <div
        id="shopFooterGrid"
        style="display:grid;grid-template-columns:minmax(260px,1.6fr) 1fr 1fr;align-items:start;gap:32px 56px;max-width:1400px;margin-left:auto;margin-right:auto;padding:56px 60px;"
    >
        {!! view_render_event('bagisto.shop.layout.footer.logo.before') !!}

        <!-- Brand: logo, description, phone -->
        <div
            id="shopFooterBrand"
            style="max-width:380px;"
        >
            <a
                href="{{ route('shop.home.index') }}"
                style="display:inline-block;margin-bottom:18px;text-decoration:none;"
            >
                @if ($channel->logo_url)
                    <img
                        src="{{ $channel->logo_url }}"
                        alt="{{ $channel->name }}"
                        style="display:block;width:auto;height:auto;max-height:44px;max-width:200px;"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='inline-block';"
                    />

                    <span style="display:none;font-size:24px;font-weight:700;color:#060C3B;">{{ $channel->name }}</span>
                @else
                    <span style="display:inline-block;font-size:24px;font-weight:700;color:#060C3B;">{{ $channel->name }}</span>
                @endif
            </a>

            <p style="margin:0 0 22px;font-size:14px;line-height:1.65;color:#71717a;">
                Welcome to {{ $channel->name }}. Explore our wide range of products with the best quality and service you can trust.
            </p>

            <div style="display:flex;align-items:center;gap:12px;">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:46px;height:46px;min-width:46px;max-width:46px;flex:0 0 46px;border-radius:10px;background-color:#060C3B;overflow:hidden;">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="21"
                        height="21"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="#ffffff"
                        stroke-width="2"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        style="display:block;width:21px;height:21px;min-width:21px;max-width:21px;flex:0 0 21px;"
                    >
                        <path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                </span>

                <span style="display:block;">
                    <span style="display:block;margin-bottom:2px;font-size:11px;font-weight:600;letter-spacing:.4px;text-transform:uppercase;color:#060C3B;">
                        Need help? Call us!
                    </span>

                    <a
                        href="tel:01779440297"
                        style="font-size:17px;font-weight:700;color:#18181b;text-decoration:none;"
                    >
                        01779440297
                    </a>
                </span>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.logo.after') !!}

        {!! view_render_event('bagisto.shop.layout.footer.links.before') !!}

        <!-- Useful links (admin-managed, with sensible defaults) -->
        @if ($usefulLinks->isNotEmpty())
            <div>
                <p style="{{ $sHeading }}">Useful Links</p>

                <ul style="{{ $sList }}">
                    @foreach ($usefulLinks as $link)
                        <li style="{{ $sListItem }}">
                            <a
                                href="{{ $link['url'] }}"
                                style="{{ $sLink }}"
                            >
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Categories, pulled live from the catalog -->
        @if ($footerCategories->isNotEmpty())
            <div>
                <p style="{{ $sHeading }}">Categories</p>

                <ul style="{{ $sList }}">
                    @foreach ($footerCategories as $category)
                        <li style="{{ $sListItem }}">
                            <a
                                href="{{ $category->url }}"
                                style="{{ $sLink }}"
                            >
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.links.after') !!}
    </div>

    <div
        id="shopFooterBar"
        style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:14px;border-top:1px solid #e4e4e7;padding:18px 60px;"
    >
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p style="margin:0;font-size:14px;color:#71717a;">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                Copyright &copy; {{ date('Y') }} {{ $channel->name }}. All rights reserved.
            @endif
        </p>

        <img
            src="https://tereazone.ae/wp-content/uploads/2025/08/Payment-method.png"
            alt="Accepted payment methods"
            style="display:block;height:24px;width:auto;max-width:100%;"
        />

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

@if ($whatsappLink || $messengerLink)
    <div class="tz-chatwidgets">
        @if ($whatsappLink)
            <a
                href="{{ $whatsappLink['url'] }}"
                class="tz-chatwidgets__btn tz-chatwidgets__btn--whatsapp"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Chat on WhatsApp"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="#ffffff"
                >
                    <path d="M12.04 2c-5.46 0-9.91 4.45-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 004.74 1.21h.005c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.87 9.87 0 0012.04 2zm5.8 14.1c-.24.68-1.4 1.33-1.94 1.4-.5.07-1.12.1-1.8-.11a16.6 16.6 0 01-1.65-.61c-2.9-1.25-4.79-4.17-4.93-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.02-2.41.27-.29.58-.36.78-.36l.56.01c.18.01.42-.07.66.5.24.58.82 2 .9 2.14.07.15.12.32.02.51-.1.19-.15.31-.3.48-.15.17-.31.38-.44.51-.15.15-.3.31-.13.6.17.29.75 1.24 1.62 2.01 1.11.99 2.05 1.3 2.34 1.44.29.15.46.13.63-.08.17-.2.72-.84.91-1.13.19-.29.38-.24.63-.14.26.1 1.65.78 1.94.92.29.14.48.21.55.33.07.12.07.68-.17 1.36z" />
                </svg>
            </a>
        @endif

        @if ($messengerLink)
            <a
                href="{{ $messengerLink['url'] }}"
                class="tz-chatwidgets__btn tz-chatwidgets__btn--messenger"
                target="_blank"
                rel="noopener noreferrer"
                aria-label="Chat on Messenger"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="28"
                    height="28"
                    viewBox="0 0 24 24"
                    fill="#ffffff"
                >
                    <path d="M12 2C6.48 2 2 6.15 2 11.27c0 2.91 1.44 5.51 3.7 7.21V22l3.38-1.86c.9.25 1.86.38 2.86.38 5.52 0 10-4.15 10-9.27S17.52 2 12 2zm1.02 12.48-2.55-2.72-4.98 2.72 5.48-5.82 2.61 2.72 4.9-2.72-5.46 5.82z" />
                </svg>
            </a>
        @endif
    </div>
@endif

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
