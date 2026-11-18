<?php

namespace Database\Seeders;

use App\Models\Bien;
use App\Models\BienHistorique;
use App\Models\Bureau;
use App\Models\CategorieBien;
use App\Models\Etage;
use App\Models\Setting;
use App\Models\TypeIncident;
use App\Models\User;
use App\Services\WhatsappService;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Comptes de démonstration (CHANGER LES MOTS DE PASSE EN PRODUCTION) ---
        $superAdmin = User::updateOrCreate(['email' => 'superadmin@hoteldeville.cd'], [
            'name' => 'Super Administrateur', 'role' => 'super_admin', 'password' => 'Admin@2026', 'phone' => '+243000000001', 'active' => true,
        ]);
        User::updateOrCreate(['email' => 'admin@hoteldeville.cd'], [
            'name' => 'Responsable Intendance', 'role' => 'admin', 'password' => 'Admin@2026', 'phone' => '+243000000002', 'active' => true,
        ]);
        $agent = User::updateOrCreate(['email' => 'agent@hoteldeville.cd'], [
            'name' => 'Agent Démo', 'role' => 'agent', 'password' => 'Agent@2026', 'phone' => '+243000000003', 'active' => true,
        ]);
        User::updateOrCreate(['email' => 'maintenance@hoteldeville.cd'], [
            'name' => 'Technicien Démo', 'role' => 'maintenance', 'password' => 'Maint@2026', 'phone' => '+243000000004', 'active' => true,
        ]);

        // --- Étages du bâtiment unique ---
        foreach (['RDC', '1er étage', '2e étage', '3e étage', '4e étage', '5e étage'] as $i => $nom) {
            Etage::firstOrCreate(['nom' => $nom], ['ordre' => $i]);
        }

        // --- Catégories de biens ---
        foreach (['Mobilier', 'Informatique', 'Électroménager', 'Climatisation', 'Éclairage / Électricité', 'Téléphonie', 'Autre'] as $nom) {
            CategorieBien::firstOrCreate(['nom' => $nom]);
        }

        // --- Types d'incidents ---
        foreach (['Climatisation', 'Électricité', 'Plomberie', 'Mobilier', 'Informatique', 'Autre'] as $nom) {
            TypeIncident::firstOrCreate(['nom' => $nom]);
        }

        // --- Paramètres WhatsApp ---
        Setting::firstOrCreate(['cle' => 'whatsapp_template'], ['valeur' => WhatsappService::DEFAULT_TEMPLATE]);
        Setting::firstOrCreate(['cle' => 'whatsapp_numeros'], ['valeur' => '']);

        // --- Données d'exemple ---
        if (Bureau::count() === 0) {
            $rdc = Etage::where('nom', 'RDC')->first();
            $e1 = Etage::where('nom', '1er étage')->first();
            $b1 = Bureau::create(['numero' => 'B-001', 'libelle' => 'Accueil', 'etage_id' => $rdc->id, 'service' => 'Accueil & Protocole']);
            $b2 = Bureau::create(['numero' => 'B-101', 'libelle' => 'Secrétariat Général', 'etage_id' => $e1->id, 'service' => 'Secrétariat']);
            $b3 = Bureau::create(['numero' => 'B-102', 'libelle' => 'Direction Informatique', 'etage_id' => $e1->id, 'service' => 'Informatique']);
            $b2->agents()->sync([$agent->id]);

            $exemples = [
                ['Bureau directeur', 'Mobilier', 'MOB-0001', $b2],
                ['Ordinateur portable', 'Informatique', 'INF-0001', $b2],
                ['Climatiseur split 18000 BTU', 'Climatisation', 'CLI-0001', $b2],
                ['Imprimante laser', 'Informatique', 'INF-0002', $b3],
                ['Poste de travail', 'Informatique', 'INF-0003', $b3],
                ['Banque d\'accueil', 'Mobilier', 'MOB-0002', $b1],
            ];
            foreach ($exemples as [$designation, $cat, $serie, $bureau]) {
                $bien = Bien::create([
                    'designation' => $designation,
                    'categorie_id' => CategorieBien::where('nom', $cat)->first()->id,
                    'numero_serie' => $serie,
                    'etat' => 'bon',
                    'bureau_id' => $bureau->id,
                    'date_acquisition' => now()->subYears(2)->toDateString(),
                ]);
                BienHistorique::create([
                    'bien_id' => $bien->id, 'user_id' => $superAdmin->id, 'action' => 'creation',
                    'nouveau_bureau_id' => $bureau->id, 'details' => 'Enregistrement initial',
                ]);
            }
        }
    }
}
