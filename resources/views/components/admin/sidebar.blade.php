<!-- Admin Sidebar Component -->
<aside class="sidebar" id="sidebar">
    <!-- Logo -->
    <div class="sidebar-header">
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <i class="bi bi-shop"></i>
            <span class="ms-2">{{ config('app.name', 'Admin') }}</span>
        </a>
        <button class="d-md-none btn btn-link text-white p-0" onclick="toggleSidebar()">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    <!-- Navigation Menu -->
    <nav class="sidebar-menu">
        <!-- Dashboard -->
        @can('dashboard.view')
            <div class="menu-item">
                <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 menu-icon"></i>
                    <span class="menu-text">{{ __('admin.dashboard') }}</span>
                </a>
            </div>
        @endcan

        <!-- Categories -->
        @can('category.view')
            <div class="menu-item">
                <a href="{{ route('admin.categories.index') }}" class="menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                    <i class="bi bi-tags menu-icon"></i>
                    <span class="menu-text">{{ __('admin.categories') }}</span>
                </a>
            </div>
        @endcan

        <!-- Products -->
        @can('product.view')
            <div class="menu-item">
                <a href="{{ route('admin.products.index') }}" class="menu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                    <i class="bi bi-box-seam menu-icon"></i>
                    <span class="menu-text">{{ __('admin.products') }}</span>
                </a>
            </div>
        @endcan

        <!-- Units -->
        @can('unit.view')
            <div class="menu-item">
                <a href="{{ route('admin.units.index') }}" class="menu-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                    <i class="bi bi-rulers menu-icon"></i>
                    <span class="menu-text">{{ __('admin.units') }}</span>
                </a>
            </div>
        @endcan

        <!-- Users -->
        @can('user.view')
            <div class="menu-item">
                <a href="{{ route('admin.users.index') }}" class="menu-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people menu-icon"></i>
                    <span class="menu-text">{{ __('admin.users') }}</span>
                </a>
            </div>
        @endcan

        <!-- Roles -->
        @can('role.view')
            <div class="menu-item">
                <a href="{{ route('admin.roles.index') }}" class="menu-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-check menu-icon"></i>
                    <span class="menu-text">{{ __('admin.roles') }}</span>
                </a>
            </div>
        @endcan

        <!-- Orders -->
        @can('order.view.all')
            <div class="menu-item">
                <a href="{{ route('admin.orders.index') }}" class="menu-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                    <i class="bi bi-receipt menu-icon"></i>
                    <span class="menu-text">{{ __('admin.orders') }}</span>
                </a>
            </div>
        @endcan

        <!-- Reviews -->
        @can('review.view')
            <div class="menu-item">
                <a href="{{ route('admin.reviews.index') }}" class="menu-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}">
                    <i class="bi bi-star-half menu-icon"></i>
                    <span class="menu-text">Reviews</span>
                </a>
            </div>
        @endcan

        <!-- Promotions -->
        @can('coupon.view')
            <div class="menu-item">
                <a href="{{ route('admin.coupons.index') }}" class="menu-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
                    <i class="bi bi-ticket-perforated menu-icon"></i>
                    <span class="menu-text">{{ __('admin.coupons') }}</span>
                </a>
            </div>
        @endcan

        <!-- Reports (placeholder) -->
        {{-- Add permissions + route later --}}

        <!-- Settings -->
        @can('setting.view')
            <div class="menu-item">
                <a href="{{ route('admin.settings.edit') }}" class="menu-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                    <i class="bi bi-gear menu-icon"></i>
                    <span class="menu-text">{{ __('admin.settings') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('admin.payment-methods.index') }}" class="menu-link {{ request()->routeIs('admin.payment-methods.*') ? 'active' : '' }}">
                    <i class="bi bi-credit-card-2-front menu-icon"></i>
                    <span class="menu-text">{{ __('admin.payment_methods') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('admin.hero-sliders.index') }}" class="menu-link {{ request()->routeIs('admin.hero-sliders.*') ? 'active' : '' }}">
                    <i class="bi bi-images menu-icon"></i>
                    <span class="menu-text">{{ __('admin.hero_slider') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('admin.cities.index') }}" class="menu-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt menu-icon"></i>
                    <span class="menu-text">{{ __('admin.cities') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('admin.delivery-categories.index') }}" class="menu-link {{ request()->routeIs('admin.delivery-categories.*') ? 'active' : '' }}">
                    <i class="bi bi-boxes menu-icon"></i>
                    <span class="menu-text">{{ __('admin.delivery_categories') }}</span>
                </a>
            </div>

            <div class="menu-item">
                <a href="{{ route('admin.delivery-types.index') }}" class="menu-link {{ request()->routeIs('admin.delivery-types.*') ? 'active' : '' }}">
                    <i class="bi bi-truck menu-icon"></i>
                    <span class="menu-text">{{ __('admin.delivery_types') }}</span>
                </a>
            </div>
        @endcan

        <!-- Divider -->
        <div class="px-3 py-2">
            <hr class="border-white opacity-25">
        </div>

        <!-- Logout -->
        <div class="menu-item mt-3">
            <form method="POST" action="{{ route('logout') }}" class="w-100">
                @csrf
                <button type="submit" class="menu-link text-start w-100 border-0 bg-transparent">
                    <i class="bi bi-box-arrow-right menu-icon"></i>
                    <span class="menu-text">{{ __('admin.logout') }}</span>
                </button>
            </form>
        </div>
    </nav>
</aside>
