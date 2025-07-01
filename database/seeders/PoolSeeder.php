<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pool;
use App\Models\Site;
use Faker\Factory as Faker;

class PoolSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('fr_FR');
        
        $sites = Site::all();
        
        if ($sites->isEmpty()) {
            $this->command->error('Aucun site trouvé. Veuillez d\'abord exécuter le SiteSeeder.');
            return;
        }

        $this->command->info('Création de pools pour chaque site...');

        // Types de pools courants dans les organisations
        $poolTypes = [
            'Formation et Éducation',
            'Santé et Bien-être',
            'Développement Économique',
            'Agriculture et Élevage',
            'Artisanat et Commerce',
            'Jeunesse et Sport',
            'Femmes et Genre',
            'Communication et Médias',
            'Environnement et Écologie',
            'Culture et Arts',
            'Technologie et Innovation',
            'Microfinance et Épargne',
            'Construction et Infrastructure',
            'Transport et Logistique',
            'Sécurité et Protection'
        ];

        $poolDescriptions = [
            'Formation et Éducation' => 'Pool dédié à l\'amélioration de l\'éducation et à la formation professionnelle des membres de la communauté.',
            'Santé et Bien-être' => 'Pool axé sur la promotion de la santé communautaire et l\'accès aux soins de santé de base.',
            'Développement Économique' => 'Pool visant à stimuler l\'économie locale et créer des opportunités d\'emploi.',
            'Agriculture et Élevage' => 'Pool spécialisé dans le développement agricole et l\'amélioration des techniques d\'élevage.',
            'Artisanat et Commerce' => 'Pool pour la promotion de l\'artisanat local et le développement du commerce.',
            'Jeunesse et Sport' => 'Pool dédié aux activités sportives et à l\'encadrement de la jeunesse.',
            'Femmes et Genre' => 'Pool axé sur l\'autonomisation des femmes et l\'égalité des genres.',
            'Communication et Médias' => 'Pool responsable de la communication interne et externe de l\'organisation.',
            'Environnement et Écologie' => 'Pool dédié à la protection de l\'environnement et au développement durable.',
            'Culture et Arts' => 'Pool pour la préservation et la promotion de la culture locale.',
            'Technologie et Innovation' => 'Pool axé sur l\'adoption des nouvelles technologies et l\'innovation.',
            'Microfinance et Épargne' => 'Pool spécialisé dans les services financiers communautaires.',
            'Construction et Infrastructure' => 'Pool dédié aux projets de construction et d\'infrastructure.',
            'Transport et Logistique' => 'Pool responsable des questions de transport et de logistique.',
            'Sécurité et Protection' => 'Pool axé sur la sécurité communautaire et la protection des membres.'
        ];

        foreach ($sites as $site) {
            // Créer 2-4 pools par site
            $numberOfPools = $faker->numberBetween(2, 4);
            $selectedPoolTypes = $faker->randomElements($poolTypes, $numberOfPools);
            
            foreach ($selectedPoolTypes as $poolType) {
                $pool = Pool::create([
                    'site_id' => $site->id,
                    'name' => $poolType . ' - ' . $site->name,
                    'description' => $poolDescriptions[$poolType] ?? 'Pool spécialisé dans le domaine ' . strtolower($poolType) . '.',
                    'is_active' => $faker->boolean(95), // 95% de chance d'être actif
                    'created_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'updated_at' => now(),
                ]);

                $this->command->info("Pool créé: {$pool->name}");
            }
        }

        $totalPools = Pool::count();
        $this->command->info("{$totalPools} pools créés avec succès !");
    }
}