# Admin Panel Layout Documentation

## Overview

This admin panel provides a modern, responsive dashboard with a collapsible sidebar navigation system. It's built with Bootstrap 5 and follows Laravel best practices.

## File Structure

```
resources/views/
├── layouts/
│   └── admin.blade.php           # Main admin layout template
├── components/admin/
│   ├── sidebar.blade.php         # Sidebar navigation component
│   └── navbar.blade.php          # Top header/navbar component
└── dashboard.blade.php           # Dashboard homepage
```

## Components

### 1. Admin Layout (`layouts/admin.blade.php`)
- **Purpose**: Main layout wrapper for all admin pages
- **Features**:
  - Responsive sidebar with toggle functionality
  - Sticky header with notifications and user menu
  - Mobile-responsive design with overlay
  - Custom CSS for professional admin appearance

### 2. Sidebar Component (`components/admin/sidebar.blade.php`)
- **Purpose**: Navigation menu with admin sections
- **Menu Items**:
  - Dashboard
  - E-Commerce (Products, Categories, Units, Orders)
  - User Management (Users, Roles, Permissions)
  - Reports & Settings
  - Help & Logout
- **Features**:
  - Collapsible on desktop
  - Sliding drawer on mobile
  - Active state highlighting
  - Icon-based navigation

### 3. Navbar Component (`components/admin/navbar.blade.php`)
- **Purpose**: Top header with user actions
- **Features**:
  - Sidebar toggle button
  - Global search bar
  - Notifications dropdown
  - Messages dropdown
  - User profile menu with logout

### 4. Dashboard (`dashboard.blade.php`)
- **Purpose**: Main admin dashboard page
- **Sections**:
  - Statistics cards (revenue, orders, products, users)
  - Sales overview chart
  - Top products list
  - Recent orders table
  - Customer activity feed
  - Quick actions panel

## Responsive Behavior

### Desktop (>768px)
- Sidebar is always visible on the left
- Can be collapsed to icon-only view
- Main content adjusts automatically
- Full header with all features

### Mobile (≤768px)
- Sidebar is hidden by default
- Slides in from left with overlay
- Toggle button to show/hide sidebar
- Simplified header on mobile
- Touch-friendly interactions

## Usage

### Using the Admin Layout
```blade
<x-admin-layout>
    <x-slot name="header">
        <!-- Custom header content -->
    </x-slot>
    
    <!-- Main page content -->
    <div class="row">
        <div class="col-12">
            <h1>Page Title</h1>
            <p>Page content goes here...</p>
        </div>
    </div>
</x-admin-layout>
```

### Adding New Pages
1. Create your Blade view in `resources/views/`
2. Use the `<x-admin-layout>` component
3. Add your page content
4. Define the route in `routes/web.php`

### Customizing the Sidebar
Edit `resources/views/components/admin/sidebar.blade.php` to:
- Add new menu items
- Change icons (uses Bootstrap Icons)
- Modify menu structure
- Update links to match your routes

## Styling

### CSS Variables
The layout uses CSS variables for easy customization:
- `--sidebar-width`: Normal sidebar width (280px)
- `--sidebar-collapsed-width`: Collapsed width (70px)
- `--header-height`: Header height (60px)

### Color Scheme
- **Primary**: Bootstrap default blue (#0d6efd)
- **Sidebar**: Dark gradient (#2c3e50 → #34495e)
- **Cards**: White with subtle shadows
- **Success**: Green for positive metrics
- **Warning**: Orange for alerts
- **Danger**: Red for critical items

## JavaScript Functionality

### Sidebar Toggle
- Desktop: Toggle between full and collapsed states
- Mobile: Show/hide with overlay
- Responsive behavior on window resize

### Active Menu Highlighting
- Automatically highlights current page based on URL
- Uses request()->routeIs() for Laravel route matching

### Interactive Elements
- Dropdown menus with Bootstrap JS
- Chart.js integration for data visualization
- Responsive tables and cards

## Integration Notes

### Laravel Authentication
- Integrates seamlessly with Laravel Breeze
- Uses existing auth middleware
- User data accessible via Auth facade

### Component Registration
Components are registered in `AppServiceProvider`:
```php
Blade::component('admin-layout', 'layouts.admin');
Blade::component('admin-sidebar', 'components.admin.sidebar');
Blade::component('admin-navbar', 'components.admin.navbar');
```

### External Dependencies
- Bootstrap 5 (CSS & JS)
- Bootstrap Icons
- Chart.js (for dashboard charts)

## Best Practices

1. **Consistent Styling**: Use Bootstrap classes and the existing CSS variables
2. **Mobile First**: Test responsive behavior on all screen sizes
3. **Accessibility**: Include proper ARIA labels and semantic HTML
4. **Performance**: Use Vite for asset optimization
5. **Security**: All forms include CSRF tokens and proper auth checks