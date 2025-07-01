<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Member;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');

        // Récupérer les données nécessaires
        $members = Member::doesntHave('user')->get();
        $roles = Role::active()->get();

        if ($members->isEmpty()) {
            $this->command->error('Aucun membre sans utilisateur trouvé. Veuillez d\'abord exécuter le MemberSeeder.');
            return;
        }

        if ($roles->isEmpty()) {
            $this->command->error('Aucun rôle trouvé. Veuillez d\'abord exécuter le RolePermissionSeeder.');
            return;
        }

        $this->command->info('Création d\'utilisateurs pour les membres...');

        // Créer un utilisateur admin par défaut
        $adminMember = $members->first();
        $adminRole = $roles->where('name', 'admin')->first();

        if ($adminRole && $adminMember) {
            User::create([
                'member_id' => $adminMember->id,
                'email' => 'admin@system.cd',
                'username' => 'admin',
                'password' => Hash::make('password123'),
                'role_id' => $adminRole->id,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info("Utilisateur admin créé: admin@system.cd / password123");
        }

        // Créer des utilisateurs pour 30% des membres restants
        $membersToCreateUsers = $members->skip(1)->take(5); // Prendre 15 membres après l'admin

        foreach ($membersToCreateUsers as $member) {
            // Sélectionner un rôle approprié selon la fonction du membre
            $role = $this->selectAppropriateRole($member, $roles);

            // Générer un nom d'utilisateur basé sur le nom du membre
            $username = $this->generateUsername($member);

            // Générer un email basé sur le nom du membre
            $email = $this->generateEmail($member);

            // Vérifier l'unicité de l'email et du username
            $emailCounter = 1;
            $usernameCounter = 1;
            $originalEmail = $email;
            $originalUsername = $username;

            while (User::where('email', $email)->exists()) {
                $email = str_replace('@', $emailCounter . '@', $originalEmail);
                $emailCounter++;
            }

            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $usernameCounter;
                $usernameCounter++;
            }

            $user = User::create([
                'member_id' => $member->id,
                'email' => $email,
                'username' => $username,
                'password' => Hash::make('password123'), // Mot de passe par défaut
                'role_id' => $role->id,
                'is_active' => $faker->boolean(95), // 95% de chance d'être actif
                'created_at' => $faker->dateTimeBetween($member->created_at, 'now'),
                'updated_at' => now(),
            ]);

            $this->command->info("Utilisateur créé: {$user->username} ({$user->email}) - Rôle: {$role->name} - Membre: {$member->full_name}");
        }

        // Créer quelques coordonateurs et chefs de pool spécifiques
        $this->createSpecialUsers($members, $roles);

        $this->command->info('Utilisateurs créés avec succès !');
        $this->command->info('Mot de passe par défaut pour tous les utilisateurs: password123');
    }

    /**
     * Sélectionner un rôle approprié selon la fonction du membre
     */
    private function selectAppropriateRole($member, $roles)
    {
        $faker = \Faker\Factory::create();

        // Si le membre a une fonction spécifique, assigner un rôle approprié
        if ($member->fonction) {
            switch (strtolower($member->fonction->nom)) {
                case 'président':
                case 'vice-président':
                case 'coordonateur':
                    $coordonateurRole = $roles->where('name', 'coordonateur')->first();
                    return $coordonateurRole ?: $roles->random();

                case 'chef de pool':
                    $chefPoolRole = $roles->where('name', 'chef_de_pool')->first();
                    return $chefPoolRole ?: $roles->random();

                default:
                    // Pour les autres fonctions, assigner aléatoirement
                    return $roles->where('name', '!=', 'admin')->random();
            }
        }

        // Si pas de fonction spécifique, assigner aléatoirement (éviter admin)
        return $roles->where('name', '!=', 'admin')->random();
    }

    /**
     * Générer un nom d'utilisateur basé sur le nom du membre
     */
    private function generateUsername($member)
    {
        $username = '';

        if ($member->firstname) {
            $username .= strtolower(substr($member->firstname, 0, 1));
        }

        if ($member->lastname) {
            $username .= strtolower($member->lastname);
        }

        if (empty($username)) {
            $username = 'user' . $member->id;
        }

        // Nettoyer le nom d'utilisateur
        $username = preg_replace('/[^a-z0-9]/', '', $username);

        return $username;
    }

    /**
     * Générer un email basé sur le nom du membre
     */
    private function generateEmail($member)
    {
        $email = '';

        if ($member->firstname) {
            $email .= strtolower($member->firstname);
        }

        if ($member->lastname) {
            if (!empty($email)) {
                $email .= '.';
            }
            $email .= strtolower($member->lastname);
        }

        if (empty($email)) {
            $email = 'user' . $member->id;
        }

        // Nettoyer l'email et ajouter le domaine
        $email = preg_replace('/[^a-z0-9.]/', '', $email);
        $email .= '@members.cd';

        return $email;
    }

    /**
     * Créer des utilisateurs spéciaux avec des rôles spécifiques
     */
    private function createSpecialUsers($members, $roles)
    {
        $faker = \Faker\Factory::create();

        // Créer 3 coordonateurs
        $coordonateurRole = $roles->where('name', 'coordonateur')->first();
        if ($coordonateurRole) {
            $coordonateurMembers = $members->where('fonction.nom', 'Coordonateur')->take(3);

            foreach ($coordonateurMembers as $member) {
                if (!$member->hasUser()) {
                    User::create([
                        'member_id' => $member->id,
                        'email' => 'coord.' . strtolower($member->lastname) . '@system.cd',
                        'username' => 'coord_' . strtolower($member->lastname),
                        'password' => Hash::make('password123'),
                        'role_id' => $coordonateurRole->id,
                        'is_active' => true,
                        'created_at' => $faker->dateTimeBetween($member->created_at, 'now'),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("Coordonateur créé: coord_{$member->lastname}");
                }
            }
        }

        // Créer 5 chefs de pool
        $chefPoolRole = $roles->where('name', 'chef_de_pool')->first();
        if ($chefPoolRole) {
            $chefPoolMembers = $members->where('fonction.nom', 'Chef de Pool')->take(5);

            foreach ($chefPoolMembers as $member) {
                if (!$member->hasUser()) {
                    User::create([
                        'member_id' => $member->id,
                        'email' => 'chef.' . strtolower($member->lastname) . '@pool.cd',
                        'username' => 'chef_' . strtolower($member->lastname),
                        'password' => Hash::make('password123'),
                        'role_id' => $chefPoolRole->id,
                        'is_active' => true,
                        'created_at' => $faker->dateTimeBetween($member->created_at, 'now'),
                        'updated_at' => now(),
                    ]);

                    $this->command->info("Chef de pool créé: chef_{$member->lastname}");
                }
            }
        }
    }
}
