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
        <div class="menu-item">
            <a href="{{ route('dashboard') }}" class="menu-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2 menu-icon"></i>
                <span class="menu-text">Dashboard</span>
            </a>
        </div>

        <!-- Categories -->
        <div class="menu-item">
            <a href="{{ route('admin.categories.index') }}" class="menu-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="bi bi-tags menu-icon"></i>
                <span class="menu-text">Categories</span>
            </a>
        </div>

        <!-- Products -->
        <div class="menu-item">
            <a href="{{ route('admin.products.index') }}" class="menu-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="bi bi-box-seam menu-icon"></i>
                <span class="menu-text">Products</span>
            </a>
        </div>

        <!-- Units -->
        <div class="menu-item">
            <a href="{{ route('admin.units.index') }}" class="menu-link {{ request()->routeIs('admin.units.*') ? 'active' : '' }}">
                <i class="bi bi-rulers menu-icon"></i>
                <span class="menu-text">Units</span>
            </a>
        </div>

        <!-- Users -->
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-people menu-icon"></i>
                <span class="menu-text">Users</span>
            </a>
        </div>

        <!-- Roles -->
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-shield-check menu-icon"></i>
                <span class="menu-text">Roles</span>
            </a>
        </div>

        <!-- Orders -->
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-receipt menu-icon"></i>
                <span class="menu-text">Orders</span>
            </a>
        </div>

        <!-- Reports -->
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-graph-up menu-icon"></i>
                <span class="menu-text">Reports</span>
            </a>
        </div>

        <!-- Settings -->
        <div class="menu-item">
            <a href="#" class="menu-link">
                <i class="bi bi-gear menu-icon"></i>
                <span class="menu-text">Settings</span>
            </a>
        </div>

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
                    <span class="menu-text">Logout</span>
                </button>
            </form>
        </div>
    </nav>
</aside>