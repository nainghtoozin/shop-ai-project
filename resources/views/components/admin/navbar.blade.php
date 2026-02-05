<!-- Admin Navbar/Header Component -->
<header class="main-header">
    <!-- Left Side -->
    <div class="d-flex align-items-center">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="bi bi-list"></i>
        </button>
        
        <!-- Search Bar -->
        <div class="ms-3 d-none d-md-block">
            <form class="position-relative">
                <input type="text" 
                       class="form-control" 
                       style="width: 300px; padding-left: 35px;" 
                       placeholder="Search...">
                <button type="submit" class="btn btn-link position-absolute" style="left: 8px; top: 50%; transform: translateY(-50%);">
                    <i class="bi bi-search text-muted"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Right Side -->
    <div class="d-flex align-items-center">
        <!-- Notifications Dropdown -->
        <div class="dropdown me-3">
            <button class="btn btn-link position-relative p-2 text-decoration-none" type="button" data-bs-toggle="dropdown">
                <i class="bi bi-bell fs-5 text-secondary"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" style="width: 300px;">
                <li class="dropdown-header">
                    <i class="bi bi-bell me-2"></i>Notifications
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="#" class="dropdown-item d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2">
                                <i class="bi bi-check-circle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1 fw-semibold">New order received</p>
                            <small class="text-muted">Order #12345 has been placed</small>
                            <div class="text-muted small">5 minutes ago</div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#" class="dropdown-item d-flex align-items-start">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-2">
                                <i class="bi bi-exclamation-triangle fs-5"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <p class="mb-1 fw-semibold">Low stock alert</p>
                            <small class="text-muted">Product XYZ is running low</small>
                            <div class="text-muted small">1 hour ago</div>
                        </div>
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a href="#" class="dropdown-item text-center">
                        View all notifications
                    </a>
                </li>
            </ul>
        </div>

        <!-- User Dropdown -->
        <div class="dropdown">
            <button class="btn btn-link d-flex align-items-center p-1 text-decoration-none" type="button" data-bs-toggle="dropdown">
                <div class="text-end me-2 d-none d-md-block">
                    <div class="fw-semibold text-dark small">{{ Auth::user()->name }}</div>
                    <small class="text-muted">{{ Auth::user()->email }}</small>
                </div>
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                    <i class="bi bi-person"></i>
                </div>
                <i class="bi bi-chevron-down text-muted ms-2"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li>
                    <a href="{{ route('profile.edit') }}" class="dropdown-item">
                        <i class="bi bi-person me-2"></i>Profile
                    </a>
                </li>
                <li>
                    <a href="{{ route('dashboard') }}" class="dropdown-item">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="dropdown-item">
                        <i class="bi bi-gear me-2"></i>Settings
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>