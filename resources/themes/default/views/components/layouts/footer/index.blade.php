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

<footer class="mt-9 bg-white max-sm:mt-10">
    <div class="flex flex-wrap justify-between gap-x-10 gap-y-8 px-[60px] py-[60px] max-md:px-8 max-md:py-8 max-sm:px-4 max-sm:py-5">
        {!! view_render_event('bagisto.shop.layout.footer.logo.before') !!}

        <!-- Logo, description and phone -->
        <div class="grid max-w-[340px] gap-4">
            <a
                href="{{ route('shop.home.index') }}"
                class="block max-w-[220px]"
            >
                <img
                    src="{{ $channel->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ $channel->name }}"
                    class="h-auto max-h-12 w-auto"
                    v-pre
                />
            </a>

            <p class="text-sm text-zinc-500">
                Welcome to {{ $channel->name }}. Explore our wide range of products with the best quality and service you can trust.
            </p>

            <div class="flex items-center gap-3">
                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-navyBlue">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-6 w-6 text-white"
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
                    <p class="text-xs font-medium uppercase text-navyBlue">
                        Need help? Call us!
                    </p>

                    <a
                        href="tel:01779440297"
                        class="text-base font-bold text-zinc-900"
                    >
                        01779440297
                    </a>
                </div>
            </div>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.logo.after') !!}

        {!! view_render_event('bagisto.shop.layout.footer.links.before') !!}

        <!-- Useful links -->
        <div class="grid gap-4 text-sm">
            <p class="text-sm font-bold uppercase text-zinc-900">
                Useful Links
            </p>

            <ul class="grid gap-3 text-sm text-zinc-500">
                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Home
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Blog
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Contact us
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Privacy Policy
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Returns Policy
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Terms &amp; Conditions
                    </a>
                </li>
            </ul>
        </div>

        <!-- Categories -->
        <div class="grid gap-4 text-sm">
            <p class="text-sm font-bold uppercase text-zinc-900">
                Categories
            </p>

            <ul class="grid gap-3 text-sm text-zinc-500">
                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 1
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 2
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 3
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 4
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 5
                    </a>
                </li>

                <li>
                    <a
                        href="#"
                        class="hover:text-navyBlue"
                    >
                        Category 6
                    </a>
                </li>
            </ul>
        </div>

        {!! view_render_event('bagisto.shop.layout.footer.links.after') !!}
    </div>

    <div class="flex flex-wrap items-center justify-between gap-4 border-t border-zinc-200 px-[60px] py-4 max-md:justify-center max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="text-sm text-zinc-500">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                Copyright &copy; {{ date('Y') }} {{ $channel->name }}, All rights reserved.
            @endif
        </p>

        <img
            src="https://tereazone.ae/wp-content/uploads/2025/08/Payment-method.png"
            alt="Accepted payment methods"
            class="h-6 w-auto max-sm:h-5"
        />

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
