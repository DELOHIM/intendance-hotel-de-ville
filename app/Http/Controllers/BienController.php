<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\BienHistorique;
use App\Models\Bureau;
use App\Models\CategorieBien;
use App\Models\Etage;
use App\Support\Activity;
use App\Support\Exporter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class BienController extends Controller
{
    private function requete(Request $request): Builder
    {
        return Bien::query()
            ->with(['categorie', 'bureau.etage'])
            ->when($request->bureau_id, fn ($q, $v) => $q->where('bureau_id', $v))
            ->when($request->etage_id, fn ($q, $v) => $q->whereHas('bureau', fn ($b) => $b->where('etage_id', $v)))
            ->when($request->categorie_id, fn ($q, $v) => $q->where('categorie_id', $v))
            ->when($request->etat, fn ($q, $v) => $q->where('etat', $v))
            ->when($request->q, fn ($q, $v) => $q->where(fn ($w) => $w->where('designation', 'like', "%$v%")->orWhere('numero_serie', 'like', "%$v%")))
            ->orderBy('designation');
    }

    public function index(Request $request)
    {
        return view('biens.index', [
            'biens' => $this->requete($request)->paginate(20)->withQueryString(),
            'etages' => Etage::orderBy('ordre')->get(),
            'bureaux' => Bureau::orderBy('numero')->get(['id', 'numero', 'libelle']),
            'categories' => CategorieBien::orderBy('nom')->get(),
        ]);
    }

    /** Export de l'inventaire (BF-BIEN-08) : ?format=csv|pdf avec les mêmes filtres que la liste. */
    public function export(Request $request)
    {
        $lignes = $this->requete($request)->get()->map(fn (Bien $b) => [
            $b->designation,
            $b->categorie->nom ?? '',
            $b->numero_serie,
            $b->etatLabel(),
            $b->bureau->numero ?? '',
            $b->bureau->etage->nom ?? '',
            optional($b->date_acquisition)->format('d/m/Y'),
        ]);

        return Exporter::sortir(
            $request->query('format', 'csv'),
            'inventaire-'.now()->format('Ymd'),
            'Inventaire des biens',
            ['Désignation', 'Catégorie', 'N° série / inventaire', 'État', 'Bureau', 'Étage', 'Acquisition'],
            $lignes
        );
    }

    public function create(Request $request)
    {
        return view('biens.form', [
            'bien' => new Bien(['etat' => 'bon', 'bureau_id' => $request->query('bureau_id')]),
            'categories' => CategorieBien::orderBy('nom')->get(),
            'bureaux' => Bureau::with('etage')->orderBy('numero')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $bien = Bien::create($this->valider($request));

        BienHistorique::create([
            'bien_id' => $bien->id,
            'user_id' => $request->user()->id,
            'action' => 'creation',
            'nouveau_bureau_id' => $bien->bureau_id,
            'details' => 'Enregistrement du bien',
        ]);
        Activity::log('bien_cree', $bien, "Création du bien {$bien->designation}");

        return redirect()->route('biens.show', $bien)->with('success', 'Bien enregistré.');
    }

    public function show(Bien $bien)
    {
        $bien->load(['categorie', 'bureau.etage', 'historiques.user', 'historiques.ancienBureau', 'historiques.nouveauBureau']);
        $incidents = $bien->incidents()->with(['type', 'bureau'])->latest()->get();

        return view('biens.show', [
            'bien' => $bien,
            'incidents' => $incidents,
            'bureaux' => Bureau::orderBy('numero')->get(['id', 'numero', 'libelle']),
        ]);
    }

    public function edit(Bien $bien)
    {
        return view('biens.form', [
            'bien' => $bien,
            'categories' => CategorieBien::orderBy('nom')->get(),
            'bureaux' => Bureau::with('etage')->orderBy('numero')->get(),
        ]);
    }

    public function update(Request $request, Bien $bien)
    {
        $data = $this->valider($request);
        $bien->fill($data);

        $modifs = [];
        foreach ($bien->getDirty() as $champ => $valeur) {
            $modifs[] = "$champ : « ".($bien->getOriginal($champ) ?? '-')." » → « ".($valeur ?? '-').' »';
        }

        $ancienBureau = $bien->getOriginal('bureau_id');
        $ancienEtat = $bien->getOriginal('etat');
        $bien->save();

        if ($modifs) {
            $transfert = (int) $ancienBureau !== (int) $bien->bureau_id;
            BienHistorique::create([
                'bien_id' => $bien->id,
                'user_id' => $request->user()->id,
                'action' => $transfert ? 'transfert' : ($ancienEtat !== $bien->etat ? 'etat' : 'modification'),
                'ancien_bureau_id' => $transfert ? $ancienBureau : null,
                'nouveau_bureau_id' => $transfert ? $bien->bureau_id : null,
                'details' => implode(' ; ', $modifs),
            ]);
            Activity::log('bien_modifie', $bien, "Modification du bien {$bien->designation}");
        }

        return redirect()->route('biens.show', $bien)->with('success', 'Bien mis à jour.');
    }

    /** Transfert rapide vers un autre bureau (avec historique, BF-BIEN-04). */
    public function transfer(Request $request, Bien $bien)
    {
        $data = $request->validate([
            'bureau_id' => ['required', 'exists:bureaux,id', Rule::notIn([$bien->bureau_id])],
            'motif' => ['nullable', 'string', 'max:500'],
        ], ['bureau_id.not_in' => 'Le bien se trouve déjà dans ce bureau.']);

        $ancien = $bien->bureau_id;
        $bien->update(['bureau_id' => $data['bureau_id']]);

        BienHistorique::create([
            'bien_id' => $bien->id,
            'user_id' => $request->user()->id,
            'action' => 'transfert',
            'ancien_bureau_id' => $ancien,
            'nouveau_bureau_id' => $bien->bureau_id,
            'details' => $data['motif'] ?? 'Transfert',
        ]);
        Activity::log('bien_transfere', $bien, "Transfert du bien {$bien->designation}");

        return back()->with('success', 'Bien transféré.');
    }

    public function destroy(Bien $bien)
    {
        $bien->delete();
        Activity::log('bien_supprime', $bien, "Suppression du bien {$bien->designation}");

        return redirect()->route('biens.index')->with('success', 'Bien supprimé (conservé dans l\'historique).');
    }

    private function valider(Request $request): array
    {
        return $request->validate([
            'designation' => ['required', 'string', 'max:190'],
            'categorie_id' => ['required', 'exists:categories_biens,id'],
            'numero_serie' => ['nullable', 'string', 'max:100'],
            'etat' => ['required', Rule::in(array_keys(config('intendance.etats_bien')))],
            'bureau_id' => ['required', 'exists:bureaux,id'],
            'date_acquisition' => ['nullable', 'date'],
            'remarques' => ['nullable', 'string', 'max:2000'],
        ]);
    }
}
