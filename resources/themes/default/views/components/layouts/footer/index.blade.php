{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);
@endphp

{{--
    Custom footer styles are pushed to the layout's @stack('styles') slot, which
    renders AFTER the compiled app.css bundle. Every rule is namespaced under
    `.esoft-footer` so it cannot collide with any global theme class, and it is
    plain CSS (no @apply / Tailwind utilities) so it renders correctly without
    rebuilding the Vite bundle.
--}}
@pushOnce('styles')
    <style>
        .esoft-footer {
            margin-top: 2.25rem;
            background-color: #ffffff;
        }

        .esoft-footer__container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px 80px;
            max-width: 1400px;
            margin-inline: auto;
            padding: 60px;
        }

        /* Left block: logo, description, phone */
        .esoft-footer__brand {
            flex: 1 1 320px;
            max-width: 360px;
        }

        .esoft-footer__logo {
            display: inline-block;
            max-width: 200px;
            margin-bottom: 20px;
        }

        .esoft-footer__logo img {
            width: auto;
            height: auto;
            max-height: 48px;
        }

        .esoft-footer__logo-text {
            font-size: 1.5rem;
            font-weight: 700;
            color: #060C3B;
            letter-spacing: .3px;
        }

        .esoft-footer__desc {
            font-size: .875rem;
            line-height: 1.6;
            color: #71717a;
            margin-bottom: 24px;
        }

        .esoft-footer__phone {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .esoft-footer__phone-icon {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            flex-shrink: 0;
            border-radius: 10px;
            background-color: #060C3B;
        }

        .esoft-footer__phone-icon svg {
            width: 22px;
            height: 22px;
            color: #ffffff;
        }

        .esoft-footer__phone-label {
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .4px;
            color: #060C3B;
            margin-bottom: 2px;
        }

        .esoft-footer__phone-number {
            font-size: 1.05rem;
            font-weight: 700;
            color: #18181b;
        }

        /* Right block: the two link columns grouped together */
        .esoft-footer__links {
            display: flex;
            flex-wrap: wrap;
            gap: 40px 90px;
        }

        .esoft-footer__col-title {
            font-size: .8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #18181b;
            margin-bottom: 20px;
        }

        .esoft-footer__col ul {
            list-style: none;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .esoft-footer__col a {
            font-size: .875rem;
            color: #71717a;
            text-decoration: none;
            transition: color .15s ease;
        }

        .esoft-footer__col a:hover {
            color: #060C3B;
        }

        /* Bottom bar: copyright + payment methods */
        .esoft-footer__bottom {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            border-top: 1px solid #e4e4e7;
            padding: 18px 60px;
        }

        .esoft-footer__copyright {
            font-size: .875rem;
            color: #71717a;
            margin: 0;
        }

        .esoft-footer__payment {
            height: 26px;
            width: auto;
        }

        /* Tablet: stack the brand block above the links */
        @media (max-width: 1024px) {
            .esoft-footer__container {
                padding: 40px 32px;
                gap: 36px;
            }

            .esoft-footer__brand {
                max-width: 100%;
                flex-basis: 100%;
            }
        }

        /* Mobile: tighten spacing and center the bottom bar */
        @media (max-width: 640px) {
            .esoft-footer__container {
                padding: 24px 16px;
                gap: 28px;
            }

            .esoft-footer__links {
                gap: 28px 48px;
            }

            .esoft-footer__bottom {
                justify-content: center;
                text-align: center;
                padding: 16px 20px;
            }

            .esoft-footer__payment {
                height: 22px;
            }
        }
    </style>
@endPushOnce

<footer class="esoft-footer">
    <div class="esoft-footer__container">
        {!! view_render_event('bagisto.shop.layout.footer.logo.before') !!}

        <!-- Logo, description and phone -->
        <div class="esoft-footer__brand">
            <a
                href="{{ route('shop.home.index') }}"
                class="esoft-footer__logo"
            >
                @if ($channel->logo_url)
                    <img
                        src="{{ $channel->logo_url }}"
                        alt="{{ $channel->name }}"
                        onerror="this.style.display='none';this.nextElementSibling.style.display='inline';"
                    />
                    <span class="esoft-footer__logo-text" style="display:none;">{{ $channel->name }}</span>
                @else
                    <span class="esoft-footer__logo-text">{{ $channel->name }}</span>
                @endif
            </a>

            <p class="esoft-footer__desc">
                Welcome to {{ $channel->name }}. Explore our wide range of products with the best quality and service you can trust.
            </p>

            <div class="esoft-footer__phone">
                <div class="esoft-footer__phone-icon">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
                        />
                    </svg>
                </div>

                <div>
                    <p class="esoft-footer__phone-label">Need help? Call us!</p>

                    <a
                        href="tel:01779440297"
                        class="esoft-footer__phone-number"
                    >
                        01779440297
                    </a>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.logo.after') !!}

        {!! view_render_event('bagisto.shop.layout.footer.links.before') !!}

        <!-- Link columns grouped together on the right -->
        <div class="esoft-footer__links">
            <!-- Useful links -->
            <div class="esoft-footer__col">
                <p class="esoft-footer__col-title">Useful Links</p>

                <ul>
                    <li><a href="#">Home</a></li>
                    <li><a href="#">Blog</a></li>
                    <li><a href="#">Contact us</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Returns Policy</a></li>
                    <li><a href="#">Terms &amp; Conditions</a></li>
                </ul>
            </div>

            <!-- Categories -->
            <div class="esoft-footer__col">
                <p class="esoft-footer__col-title">Categories</p>

                <ul>
                    <li><a href="#">Category 1</a></li>
                    <li><a href="#">Category 2</a></li>
                    <li><a href="#">Category 3</a></li>
                    <li><a href="#">Category 4</a></li>
                    <li><a href="#">Category 5</a></li>
                    <li><a href="#">Category 6</a></li>
                </ul>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.links.after') !!}
    </div>

    <div class="esoft-footer__bottom">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="esoft-footer__copyright">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                Copyright &copy; {{ date('Y') }} {{ $channel->name }}, All rights reserved.
            @endif
        </p>

        <img
            class="esoft-footer__payment"
            src="https://tereazone.ae/wp-content/uploads/2025/08/Payment-method.png"
            alt="Accepted payment methods"
        />

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
