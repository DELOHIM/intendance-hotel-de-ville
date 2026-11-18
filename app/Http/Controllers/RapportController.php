<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienHistorique;
use App\Models\Bureau;
use App\Models\CategorieBien;
use App\Models\Etage;
use App\Models\Incident;
use App\Models\TypeIncident;
use App\Support\Exporter;
use Illuminate\Http\Request;

class RapportController extends Controller
{
    public function index()
    {
        return view('rapports.index', [
            'etages' => Etage::orderBy('ordre')->get(),
            'bureaux' => Bureau::orderBy('numero')->get(['id', 'numero', 'libelle']),
            'types' => TypeIncident::orderBy('nom')->get(),
            'categories' => CategorieBien::orderBy('nom')->get(),
        ]);
    }

    public function inventaire(Request $request)
    {
        $biens = Bien::with(['categorie', 'bureau.etage'])
            ->when($request->bureau_id, fn ($q, $v) => $q->where('bureau_id', $v))
            ->when($request->etage_id, fn ($q, $v) => $q->whereHas('bureau', fn ($b) => $b->where('etage_id', $v)))
            ->when($request->categorie_id, fn ($q, $v) => $q->where('categorie_id', $v))
            ->when($request->etat, fn ($q, $v) => $q->where('etat', $v))
            ->get()->sortBy(fn ($b) => ($b->bureau->numero ?? '').$b->designation);

        $lignes = $biens->map(fn ($b) => [
            $b->bureau->numero ?? '', $b->bureau->etage->nom ?? '', $b->designation, $b->categorie->nom ?? '',
            $b->numero_serie, $b->etatLabel(), optional($b->date_acquisition)->format('d/m/Y'),
        ]);

        return Exporter::sortir(
            $request->query('format', 'pdf'), 'rapport-inventaire-'.now()->format('Ymd'), 'Rapport d\'inventaire',
            ['Bureau', 'Étage', 'Désignation', 'Catégorie', 'N° série', 'État', 'Acquisition'], $lignes,
            $request->only(['bureau_id', 'etage_id', 'categorie_id', 'etat'])
        );
    }

    public function incidents(Request $request)
    {
        $incidents = Incident::with(['bureau.etage', 'type', 'declarant', 'assigne'])
            ->filtre($request->only(['du', 'au', 'etage_id', 'bureau_id', 'type_incident_id', 'statut', 'priorite']))
            ->latest()->get();

        $lignes = $incidents->map(fn ($i) => [
            $i->reference, $i->created_at->format('d/m/Y H:i'), $i->bureau->numero ?? '', $i->bureau->etage->nom ?? '',
            $i->type->nom ?? '', $i->prioriteLabel(), $i->statutLabel(), $i->declarant->name ?? '',
            $i->assigne->name ?? '', optional($i->resolu_le)->format('d/m/Y H:i'), $i->description,
        ]);

        return Exporter::sortir(
            $request->query('format', 'pdf'), 'rapport-incidents-'.now()->format('Ymd'), 'Rapport des incidents',
            ['Référence', 'Date', 'Bureau', 'Étage', 'Type', 'Priorité', 'Statut', 'Déclarant', 'Assigné à', 'Résolu le', 'Description'],
            $lignes, $request->only(['du', 'au', 'etage_id', 'bureau_id', 'type_incident_id', 'statut', 'priorite'])
        );
    }

    /** Historique des mouvements/modifications des biens. */
    public function historique(Request $request)
    {
        $histo = BienHistorique::with(['bien', 'user', 'ancienBureau', 'nouveauBureau'])
            ->when($request->du, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($request->au, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()->get();

        $lignes = $histo->map(fn ($h) => [
            $h->created_at->format('d/m/Y H:i'), $h->bien->designation ?? '(supprimé)', $h->action,
            $h->ancienBureau->numero ?? '', $h->nouveauBureau->numero ?? '', $h->user->name ?? '', $h->details,
        ]);

        return Exporter::sortir(
            $request->query('format', 'pdf'), 'rapport-historique-'.now()->format('Ymd'), 'Historique des biens',
            ['Date', 'Bien', 'Action', 'De', 'Vers', 'Par', 'Détails'], $lignes, $request->only(['du', 'au'])
        );
    }
}
