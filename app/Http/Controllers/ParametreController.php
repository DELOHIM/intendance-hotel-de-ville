<?php

namespace App\Http\Controllers;

use App\Models\CategorieBien;
use App\Models\Etage;
use App\Models\TypeIncident;
use App\Support\Activity;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParametreController extends Controller
{
    /** Ressources paramétrables : clé de route => [modèle, table, libellé] */
    private const RESSOURCES = [
        'etages' => [Etage::class, 'etages', 'Étage'],
        'categories' => [CategorieBien::class, 'categories_biens', 'Catégorie de bien'],
        'types' => [TypeIncident::class, 'types_incidents', "Type d'incident"],
    ];

    public function index()
    {
        return view('parametres.index', [
            'etages' => Etage::withCount('bureaux')->orderBy('ordre')->get(),
            'categories' => CategorieBien::withCount('biens')->orderBy('nom')->get(),
            'types' => TypeIncident::withCount('incidents')->orderBy('nom')->get(),
        ]);
    }

    public function store(Request $request, string $kind)
    {
        [$modele, $table, $label] = $this->ressource($kind);

        $data = $request->validate($this->regles($kind, $table));
        $objet = $modele::create($data);
        Activity::log('parametre_cree', $objet, "$label : {$objet->nom}");

        return back()->with('success', "$label ajouté(e).");
    }

    public function update(Request $request, string $kind, int $id)
    {
        [$modele, $table, $label] = $this->ressource($kind);
        $objet = $modele::findOrFail($id);

        $data = $request->validate($this->regles($kind, $table, $id));
        $objet->update($data);
        Activity::log('parametre_modifie', $objet, "$label : {$objet->nom}");

        return back()->with('success', "$label mis(e) à jour.");
    }

    public function destroy(string $kind, int $id)
    {
        [$modele, , $label] = $this->ressource($kind);
        $objet = $modele::findOrFail($id);

        try {
            $objet->delete();
        } catch (QueryException $e) {
            return back()->with('error', "Suppression impossible : cet élément est encore utilisé.");
        }

        Activity::log('parametre_supprime', $objet, "$label : {$objet->nom}");

        return back()->with('success', "$label supprimé(e).");
    }

    private function ressource(string $kind): array
    {
        abort_unless(isset(self::RESSOURCES[$kind]), 404);

        return self::RESSOURCES[$kind];
    }

    private function regles(string $kind, string $table, ?int $ignore = null): array
    {
        $regles = ['nom' => ['required', 'string', 'max:100', Rule::unique($table, 'nom')->ignore($ignore)]];

        if ($kind === 'etages') {
            $regles['ordre'] = ['nullable', 'integer', 'min:0', 'max:200'];
        }

        return $regles;
    }
}
