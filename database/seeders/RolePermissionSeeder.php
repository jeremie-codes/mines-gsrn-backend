<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Créer les permissions
        $permissions = [
            ['name' => 'site.view', 'description' => 'Voir les sites'],
            ['name' => 'site.create', 'description' => 'Créer des sites'],
            ['name' => 'site.edit', 'description' => 'Modifier les sites'],
            ['name' => 'site.delete', 'description' => 'Supprimer les sites'],
            
            ['name' => 'pool.view', 'description' => 'Voir les pools'],
            ['name' => 'pool.create', 'description' => 'Créer des pools'],
            ['name' => 'pool.edit', 'description' => 'Modifier les pools'],
            ['name' => 'pool.delete', 'description' => 'Supprimer les pools'],
            
            ['name' => 'member.view', 'description' => 'Voir les membres'],
            ['name' => 'member.create', 'description' => 'Créer des membres'],
            ['name' => 'member.edit', 'description' => 'Modifier les membres'],
            ['name' => 'member.delete', 'description' => 'Supprimer les membres'],
            
            ['name' => 'user.view', 'description' => 'Voir les utilisateurs'],
            ['name' => 'user.create', 'description' => 'Créer des utilisateurs'],
            ['name' => 'user.edit', 'description' => 'Modifier les utilisateurs'],
            ['name' => 'user.delete', 'description' => 'Supprimer les utilisateurs'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

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

        // Assigner toutes les permissions à l'admin
        $adminRole->permissions()->attach(Permission::all());

        // Assigner des permissions limitées au coordonateur
        $coordonateurPermissions = Permission::whereIn('name', [
            'site.view', 'pool.view', 'pool.create', 'pool.edit',
            'member.view', 'member.create', 'member.edit',
            'user.view', 'user.create'
        ])->get();
        $coordonateurRole->permissions()->attach($coordonateurPermissions);

        // Assigner des permissions très limitées au chef de pool
        $chefPoolPermissions = Permission::whereIn('name', [
            'pool.view', 'member.view', 'member.edit'
        ])->get();
        $chefPoolRole->permissions()->attach($chefPoolPermissions);
    }
}