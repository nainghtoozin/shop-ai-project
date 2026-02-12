<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            // Unit
            'unit.view',
            'unit.create',
            'unit.edit',
            'unit.delete',

            // Categories
            'category.view',
            'category.create',
            'category.edit',
            'category.delete',

            // Products
            'product.view',
            'product.create',
            'product.edit',
            'product.delete',

            // Orders
            'order.view.own',
            'order.view.all',
            'order.edit',
            'order.delete',

            // Settings
            'setting.view',
            'setting.edit',

            // Coupons
            'coupon.view',
            'coupon.create',
            'coupon.edit',
            'coupon.delete',

            // Dashboard
            'dashboard.view',
            'dashboard.view.own_income',
            'dashboard.view.all_income',

            // Users
            'user.view',
            'user.create',
            'user.edit',
            'user.delete',

            // Roles
            'role.view',
            'role.create',
            'role.edit',
            'role.delete',
        ];

        foreach ($permissions as $name) {
            Permission::findOrCreate($name, 'web');
        }
    }
}
