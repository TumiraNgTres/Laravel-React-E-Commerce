<?php

namespace Database\Seeders;

use App\Enum\PermissionsEnum;
use App\Enum\RolesEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // define roles and store in role table

        $userRole = Role::create([
            'name' => RolesEnum::User->value
        ]);
        $vendorRole = Role::create([
            'name' => RolesEnum::Vendor->value
        ]);
        $adminRole = Role::create([
            'name' => RolesEnum::Admin->value
        ]);

        // define permissions and store in permision table

        $approveVendors = Permission::create([
            'name' => PermissionsEnum::ApproveVendors->value
        ]);
        $sellProducts = Permission::create([
            'name' => PermissionsEnum::SellProducts->value
        ]);
        $buyProducts = Permission::create([
            'name' => PermissionsEnum::BuyProducts->value
        ]);

        // associate permissions with the roles

        $userRole->syncPermissions([
            $buyProducts
        ]);
        $vendorRole->syncPermissions([
            $sellProducts,
            $buyProducts
        ]);
        $adminRole->syncPermissions([
            $approveVendors,
            $sellProducts,
            $buyProducts
        ]);
    }
}
