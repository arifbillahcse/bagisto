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
    $reservedForHeader = ['Top Bar Contact', 'Top Bar Links', 'Header Links'];

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

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
