<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Bureau;
use App\Models\Etage;
use App\Models\Incident;
use App\Models\TypeIncident;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            return $this->admin($request);
        }

        if ($user->isMaintenance()) {
            return view('dashboard.agent', [
                'bureaux' => collect(),
                'incidents' => Incident::with(['bureau', 'type'])->where('assigne_a', $user->id)->latest()->limit(15)->get(),
                'compteurs' => [
                    'a_traiter' => Incident::where('assigne_a', $user->id)->ouverts()->count(),
                    'resolus' => Incident::where('assigne_a', $user->id)->where('statut', 'resolu')->count(),
                ],
                'maintenance' => true,
            ]);
        }

        return view('dashboard.agent', [
            'bureaux' => $user->bureaux()->with('etage')->get(),
            'incidents' => Incident::with(['bureau', 'type'])->where('user_id', $user->id)->latest()->limit(15)->get(),
            'compteurs' => [
                'a_traiter' => Incident::where('user_id', $user->id)->ouverts()->count(),
                'resolus' => Incident::where('user_id', $user->id)->where('statut', 'resolu')->count(),
            ],
            'maintenance' => false,
        ]);
    }

    private function admin(Request $request)
    {
        $filtres = $request->only(['du', 'au', 'etage_id', 'bureau_id', 'type_incident_id', 'statut']);
        $incidents = Incident::with(['bureau.etage', 'type'])->filtre($filtres)->get();

        $parMois = $incidents->groupBy(fn ($i) => $i->created_at->format('Y-m'))->map->count()->sortKeys();
        $parType = $incidents->groupBy(fn ($i) => $i->type->nom ?? '-')->map->count()->sortDesc();
        $parEtage = $incidents->groupBy(fn ($i) => $i->bureau->etage->nom ?? '-')->map->count();
        $parBureau = $incidents->groupBy(fn ($i) => $i->bureau->numero ?? '-')->map->count()->sortDesc()->take(10);
        $parStatut = $incidents->groupBy('statut')->map->count();

        $ouverts = Incident::ouverts();
        $parPriorite = (clone $ouverts)->selectRaw('priorite, count(*) as total')->groupBy('priorite')->pluck('total', 'priorite');

        return view('dashboard.admin', [
            'stats' => [
                'bureaux' => Bureau::count(),
                'biens' => Bien::count(),
                'en_cours' => (clone $ouverts)->count(),
                'critiques' => (clone $ouverts)->where('priorite', 'critique')->count(),
            ],
            'parPriorite' => $parPriorite,
            'derniers' => Incident::with(['bureau', 'type'])->where('statut', 'nouveau')->latest()->limit(8)->get(),
            'graph' => [
                'mois' => ['labels' => $parMois->keys()->values(), 'data' => $parMois->values()],
                'type' => ['labels' => $parType->keys()->values(), 'data' => $parType->values()],
                'etage' => ['labels' => $parEtage->keys()->values(), 'data' => $parEtage->values()],
                'bureau' => ['labels' => $parBureau->keys()->values(), 'data' => $parBureau->values()],
                'statut' => [
                    'labels' => $parStatut->keys()->map(fn ($k) => config('intendance.statuts_incident.'.$k, $k))->values(),
                    'data' => $parStatut->values(),
                ],
            ],
            'filtres' => $filtres,
            'totalFiltre' => $incidents->count(),
            'etages' => Etage::orderBy('ordre')->get(),
            'bureaux' => Bureau::orderBy('numero')->get(['id', 'numero', 'libelle']),
            'types' => TypeIncident::orderBy('nom')->get(),
        ]);
    }
}
