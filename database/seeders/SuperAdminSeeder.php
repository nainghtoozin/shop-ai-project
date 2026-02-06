<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure permissions exist
        $this->call(PermissionSeeder::class);

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::findOrCreate('super-admin', 'web');
        $role->syncPermissions(Permission::query()->where('guard_name', 'web')->get());

        $user = User::query()->updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Users should get permissions via roles
        $user->syncRoles([$role]);
    }
}
