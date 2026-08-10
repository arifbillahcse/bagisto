{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')
@inject('categoryRepository', 'Webkul\Category\Repositories\CategoryRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    /**
     * Top-level visible categories for the "Categories" column
     * (children of the channel's root category, max 6).
     */
    $footerCategories = collect($categoryRepository->getVisibleCategoryTree($channel->root_category_id))->take(6);
@endphp

<style>
    .tzf {
        margin-top: 2.25rem;
        background-color: #ffffff;
    }

    .tzf__container {
        display: grid;
        grid-template-columns: minmax(260px, 1.6fr) 1fr 1fr;
        align-items: start;
        gap: 32px 56px;
        max-width: 1400px;
        margin-inline: auto;
        padding: 60px;
    }

    /* Left block: logo, description, phone */
    .tzf__brand {
        max-width: 380px;
    }

    .tzf__logo {
        display: inline-block;
        max-width: 200px;
        margin-bottom: 20px;
    }

    .tzf__logo img {
        width: auto;
        height: auto;
        max-height: 48px;
    }

    .tzf__logo-text {
        font-size: 1.5rem;
        font-weight: 700;
        color: #060C3B;
        letter-spacing: .3px;
    }

    .tzf__desc {
        font-size: .875rem;
        line-height: 1.6;
        color: #71717a;
        margin-bottom: 24px;
    }

    .tzf__phone {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .tzf__phone-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        flex-shrink: 0;
        border-radius: 10px;
        background-color: #060C3B;
        overflow: hidden;
    }

    .tzf__phone-icon svg {
        display: block;
        width: 22px;
        height: 22px;
        color: #ffffff;
    }

    .tzf__phone-label {
        font-size: .72rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .4px;
        color: #060C3B;
        margin-bottom: 2px;
    }

    .tzf__phone-number {
        font-size: 1.05rem;
        font-weight: 700;
        color: #18181b;
    }

    .tzf__col-title {
        font-size: .8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .5px;
        color: #18181b;
        margin-bottom: 20px;
    }

    .tzf__col ul {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .tzf__col a {
        font-size: .875rem;
        color: #71717a;
        text-decoration: none;
        transition: color .15s ease;
    }

    .tzf__col a:hover {
        color: #060C3B;
    }

    /* Bottom bar: copyright + payment methods */
    .tzf__bottom {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        border-top: 1px solid #e4e4e7;
        padding: 18px 60px;
    }

    .tzf__copyright {
        font-size: .875rem;
        color: #71717a;
        margin: 0;
    }

    .tzf__payment {
        height: 26px;
        width: auto;
    }

    /* Tablet: brand spans the full width above two link columns */
    @media (max-width: 1024px) {
        .tzf__container {
            grid-template-columns: 1fr 1fr;
            padding: 40px 32px;
            gap: 36px 48px;
        }

        .tzf__brand {
            grid-column: 1 / -1;
            max-width: 100%;
        }
    }

    /* Mobile: tighten spacing and center the bottom bar */
    @media (max-width: 640px) {
        .tzf__container {
            padding: 24px 16px;
            gap: 28px 32px;
        }

        .tzf__bottom {
            justify-content: center;
            text-align: center;
            padding: 16px 20px;
        }

        .tzf__payment {
            height: 22px;
        }
    }
</style>

<footer class="tzf">
    <div class="tzf__container">
        {!! view_render_event('bagisto.shop.layout.footer.logo.before') !!}

        <!-- Logo, description and phone -->
        <div class="tzf__brand">
            <a
                href="{{ route('shop.home.index') }}"
                class="tzf__logo"
            >
                @if ($channel->logo_url)
                    <img
                        src="{{ $channel->logo_url }}"
                        alt="{{ $channel->name }}"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='inline';"
                    />
                    <span class="tzf__logo-text" style="display:none;">{{ $channel->name }}</span>
                @else
                    <span class="tzf__logo-text">{{ $channel->name }}</span>
                @endif
            </a>

            <p class="tzf__desc">
                Welcome to {{ $channel->name }}. Explore our wide range of products with the best quality and service you can trust.
            </p>

            <div class="tzf__phone">
                <div class="tzf__phone-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="22"
                        height="22"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                        style="width:22px;height:22px;"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                        />
                    </svg>
                </div>

                <div>
                    <p class="tzf__phone-label">Need help? Call us!</p>

                    <a
                        href="tel:01779440297"
                        class="tzf__phone-number"
                    >
                        01779440297
                    </a>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.logo.after') !!}

        {!! view_render_event('bagisto.shop.layout.footer.links.before') !!}

        <!-- Useful links -->
        <div class="tzf__col">
            <p class="tzf__col-title">Useful Links</p>

            <ul>
                <li><a href="{{ route('shop.home.index') }}">Home</a></li>
                <li><a href="{{ route('shop.cms.page', 'about-us') }}">About Us</a></li>
                <li><a href="{{ route('shop.home.contact_us') }}">Contact us</a></li>
                <li><a href="{{ route('shop.cms.page', 'privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ route('shop.cms.page', 'return-policy') }}">Returns Policy</a></li>
                <li><a href="{{ route('shop.cms.page', 'terms-conditions') }}">Terms &amp; Conditions</a></li>
            </ul>
        </div>

        <!-- Categories (real top-level categories from the catalog) -->
        @if ($footerCategories->isNotEmpty())
            <div class="tzf__col">
                <p class="tzf__col-title">Categories</p>

                <ul>
                    @foreach ($footerCategories as $category)
                        <li><a href="{{ $category->url }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.links.after') !!}
    </div>

    <div class="tzf__bottom">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="tzf__copyright">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                Copyright &copy; {{ date('Y') }} {{ $channel->name }}, All rights reserved.
            @endif
        </p>

        <img
            class="tzf__payment"
            src="https://tereazone.ae/wp-content/uploads/2025/08/Payment-method.png"
            alt="Accepted payment methods"
        />

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
