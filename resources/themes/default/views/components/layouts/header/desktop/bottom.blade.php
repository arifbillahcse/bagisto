{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

{{--
    Theme override of the core desktop header row.

    Differences from core:
      - The category navigation is no longer part of this row; it renders as its
        own full-width bar underneath (see `v-category-bar` below).
      - The right-hand actions are icon + label pairs (Track Order, Sign In,
        Wishlist, Compare, Cart) instead of bare icons.

    Search, the mini-cart drawer, and the account dropdown are carried over from
    core unchanged, including their WebMCP tool attributes and render events.

    Styling for both rows lives in the header's `index.blade.php` override,
    because this file is emitted inside a `<script type="text/x-template">` block
    where a `<style>` tag would not apply.
--}}

<!-- Main row: logo, search, actions -->
<div class="rnj-mainrow">
    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

    <a
        href="{{ route('shop.home.index') }}"
        class="rnj-mainrow__logo"
        aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.bagisto')"
    >
        <img
            src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
            width="131"
            height="29"
            alt="{{ config('app.name') }}"
        >
    </a>

    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}

    <!-- Search -->
    <div class="rnj-mainrow__search">
        <form
            action="{{ route('shop.search.index') }}"
            class="rnj-search"
            role="search"
            toolname="search_products"
            tooldescription="{{ trans('shop::app.components.layouts.webmcp.search-products') }}"
            toolautosubmit
        >
            <label
                for="organic-search"
                class="sr-only"
            >
                @lang('shop::app.components.layouts.header.desktop.bottom.search')
            </label>

            <div class="icon-search rnj-search__icon"></div>

            <input
                type="text"
                name="query"
                value="{{ request('query') }}"
                toolparamdescription="{{ trans('shop::app.components.layouts.webmcp.search-products-query') }}"
                class="rnj-search__input"
                minlength="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
                maxlength="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
                placeholder="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
                aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
                aria-required="true"
                pattern="[^\\]+"
                required
            >

            <button
                type="submit"
                class="hidden"
                aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')"
            >
            </button>

            @if (core()->getConfigData('catalog.products.settings.image_search'))
                @include('shop::search.images.index')
            @endif
        </form>
    </div>

    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}

    <!-- Actions -->
    <div class="rnj-actions">
        <!-- Track order -->
        <a
            href="{{ route('shop.customers.account.orders.index') }}"
            class="rnj-action"
        >
            <span
                class="icon-truck rnj-action__icon"
                role="presentation"
            ></span>

            <span class="rnj-action__label">
                @lang('shop::app.components.layouts.header.desktop.bottom.orders')
            </span>
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

        <!-- Account -->
        <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
            <x-slot:toggle>
                <span
                    class="rnj-action"
                    role="button"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.profile')"
                    tabindex="0"
                >
                    <span
                        class="icon-users rnj-action__icon"
                        role="presentation"
                    ></span>

                    <span class="rnj-action__label">
                        @guest('customer')
                            @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                        @endguest

                        @auth('customer')
                            {{ auth()->guard('customer')->user()->first_name }}
                        @endauth
                    </span>
                </span>
            </x-slot>

            <!-- Guest Dropdown -->
            @guest('customer')
                <x-slot:content>
                    <div class="grid gap-2.5">
                        <p class="text-xl font-dmserif">
                            @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                        </p>

                        <p class="text-sm">
                            @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                        </p>
                    </div>

                    <p class="w-full mt-3 border border-zinc-200"></p>

                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.before') !!}

                    <div class="flex gap-4 mt-6">
                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_in_button.before') !!}

                        <a
                            href="{{ route('shop.customer.session.create') }}"
                            class="block m-0 mx-auto text-base text-center primary-button w-max rounded-2xl px-7 max-md:rounded-lg ltr:ml-0 rtl:mr-0"
                        >
                            @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                        </a>

                        <a
                            href="{{ route('shop.customers.register.index') }}"
                            class="block m-0 mx-auto text-base text-center border-2 secondary-button w-max rounded-2xl px-7 max-md:rounded-lg max-md:py-3 ltr:ml-0 rtl:mr-0"
                        >
                            @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                        </a>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_up_button.after') !!}
                    </div>

                    @if (core()->getConfigData('sales.eu_withdrawal.general.enabled', core()->getCurrentChannelCode()))
                        <a
                            href="{{ route('shop.eu-withdrawal.guest.lookup') }}"
                            class="mt-4 inline-flex items-center gap-1.5 text-xs font-medium text-navyBlue hover:underline"
                        >
                            @lang('shop::app.eu_withdrawal.guest_dropdown.link')
                        </a>
                    @endif

                    {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.after') !!}
                </x-slot>
            @endguest

            <!-- Customers Dropdown -->
            @auth('customer')
                <x-slot:content class="!p-0">
                    <div class="grid gap-2.5 p-5 pb-0">
                        <p class="text-xl font-dmserif" v-pre>
                            @lang('shop::app.components.layouts.header.desktop.bottom.welcome')’
                            {{ auth()->guard('customer')->user()->first_name }}
                        </p>

                        <p class="text-sm">
                            @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                        </p>
                    </div>

                    <p class="w-full mt-3 border border-zinc-200"></p>

                    <div class="mt-2.5 grid gap-1 pb-2.5">
                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}

                        <a
                            class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                            href="{{ route('shop.customers.account.profile.index') }}"
                        >
                            @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                        </a>

                        <a
                            class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                            href="{{ route('shop.customers.account.orders.index') }}"
                        >
                            @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                        </a>

                        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                            <a
                                class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                href="{{ route('shop.customers.account.wishlist.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                            </a>
                        @endif

                        <!--Customers logout-->
                        @auth('customer')
                            <x-shop::form
                                method="DELETE"
                                action="{{ route('shop.customer.session.destroy') }}"
                                id="customerLogout"
                            />

                            <a
                                class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                href="{{ route('shop.customer.session.destroy') }}"
                                onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                            </a>
                        @endauth

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                    </div>
                </x-slot>
            @endauth
        </x-shop::dropdown>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}

        <!-- Wishlist -->
        @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
            <a
                href="{{ route('shop.customers.account.wishlist.index') }}"
                class="rnj-action"
            >
                <span
                    class="icon-heart rnj-action__icon"
                    role="presentation"
                ></span>

                <span class="rnj-action__label">
                    @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                </span>
            </a>
        @endif

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}

        <!-- Compare -->
        @if(core()->getConfigData('catalog.products.settings.compare_option'))
            <a
                href="{{ route('shop.compare.index') }}"
                class="rnj-action"
            >
                <span
                    class="icon-compare rnj-action__icon"
                    role="presentation"
                ></span>

                <span class="rnj-action__label">
                    @lang('shop::app.components.layouts.header.desktop.bottom.compare')
                </span>
            </a>
        @endif

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}

        <!-- Mini cart -->
        @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
            <span class="rnj-action rnj-action--cart">
                @include('shop::checkout.cart.mini-cart')

                <span class="rnj-action__label">
                    @lang('shop::app.checkout.cart.mini-cart.shopping-cart')
                </span>
            </span>
        @endif

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}
    </div>
</div>

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

<!-- Category bar -->
<div class="rnj-catbar">
    <v-category-bar>
        <div class="rnj-catbar__inner">
            <span
                class="shimmer h-5 w-20 rounded"
                role="presentation"
            ></span>

            <span
                class="shimmer h-5 w-20 rounded"
                role="presentation"
            ></span>

            <span
                class="shimmer h-5 w-20 rounded"
                role="presentation"
            ></span>
        </div>
    </v-category-bar>
</div>

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-category-bar-template"
    >
        <div class="rnj-catbar__inner">
            <div
                class="rnj-catbar__item"
                v-for="category in categories"
                :key="category.id"
            >
                <a
                    :href="category.url"
                    class="rnj-catbar__link"
                >
                    @{{ category.name }}

                    <span
                        class="icon-arrow-down rnj-catbar__caret"
                        v-if="category.children && category.children.length"
                        role="presentation"
                    ></span>
                </a>

                <!-- Sub-categories -->
                <div
                    class="rnj-catbar__panel"
                    v-if="category.children && category.children.length"
                >
                    <div class="rnj-catbar__columns">
                        <div
                            class="rnj-catbar__column"
                            v-for="child in category.children"
                            :key="child.id"
                        >
                            <a
                                :href="child.url"
                                class="rnj-catbar__child"
                            >
                                @{{ child.name }}
                            </a>

                            <ul
                                class="rnj-catbar__grandchildren"
                                v-if="child.children && child.children.length"
                            >
                                <li
                                    v-for="grandChild in child.children"
                                    :key="grandChild.id"
                                >
                                    <a
                                        :href="grandChild.url"
                                        class="rnj-catbar__grandchild"
                                    >
                                        @{{ grandChild.name }}
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-category-bar', {
            template: '#v-category-bar-template',

            data() {
                return {
                    categories: [],
                };
            },

            mounted() {
                this.initCategories();
            },

            methods: {
                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);

                            return;
                        }
                    } catch (e) {}

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.categories = response.data.data;

                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(error => {
                            console.log(error);
                        });
                },
            },
        });
    </script>
@endPushOnce

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}
