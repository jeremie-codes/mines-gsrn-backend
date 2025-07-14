<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {

        // Créer les rôles
        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrateur système',
            'is_active' => true
        ]);

        $coordonateurRole = Role::create([
            'name' => 'coordonateur',
            'description' => 'Coordonateur',
            'is_active' => true
        ]);

        $chefPoolRole = Role::create([
            'name' => 'chef_de_pool',
            'description' => 'Chef de pool',
            'is_active' => true
        ]);

    }
}