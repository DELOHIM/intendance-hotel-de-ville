@extends('layouts.app')

@section('title', 'Rapports')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Rapports et exports</h1>
        <p class="text-slate-500 mt-1 text-sm">Générez et téléchargez des rapports détaillés sur l'inventaire, les incidents et l'historique.</p>
    </div>

    @php
        $cartes = [
            [
                'route' => 'rapports.inventaire', 
                'titre' => "Rapport d'inventaire", 
                'desc' => "Liste complète des biens avec filtres par étage, bureau, catégorie et état.", 
                'champs' => ['etage_id', 'bureau_id', 'categorie_id', 'etat'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                'color' => 'brand-blue',
            ],
            [
                'route' => 'rapports.incidents', 
                'titre' => 'Rapport des incidents', 
                'desc' => "Analyse des incidents sur une période, avec filtres par étage, bureau, type, statut et priorité.", 
                'champs' => ['du', 'au', 'etage_id', 'bureau_id', 'type_incident_id', 'statut', 'priorite'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>',
                'color' => 'brand-red',
            ],
            [
                'route' => 'rapports.historique', 
                'titre' => 'Historique des biens', 
                'desc' => "Journal des créations, modifications et transferts de biens sur une période donnée.", 
                'champs' => ['du', 'au'],
                'icon' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>',
                'color' => 'emerald',
            ],
        ];
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        @foreach ($cartes as $carte)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                
                {{-- En-tête de la carte --}}
                <div class="p-5 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-{{ $carte['color'] }}/10 text-{{ $carte['color'] }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $carte['icon'] !!}</svg>
                        </div>
                        <h2 class="font-heading font-semibold text-slate-800">{{ $carte['titre'] }}</h2>
                    </div>
                    <p class="text-sm text-slate-500 leading-relaxed">{{ $carte['desc'] }}</p>
                </div>

                {{-- Formulaire --}}
                <form method="GET" action="{{ route($carte['route']) }}" class="p-5 space-y-4 flex-1 flex flex-col text-sm">
                    
                    {{-- Dates --}}
                    @if (in_array('du', $carte['champs']))
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Du</label>
                                <input type="date" name="du" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Au</label>
                                <input type="date" name="au" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                            </div>
                        </div>
                    @endif

                    {{-- Étage --}}
                    @if (in_array('etage_id', $carte['champs']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Étage</label>
                            <select name="etage_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                <option value="">Tous les étages</option>
                                @foreach ($etages as $e)
                                    <option value="{{ $e->id }}">{{ $e->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Bureau --}}
                    @if (in_array('bureau_id', $carte['champs']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Bureau</label>
                            <select name="bureau_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                <option value="">Tous les bureaux</option>
                                @foreach ($bureaux as $b)
                                    <option value="{{ $b->id }}">{{ $b->numero }} — {{ $b->libelle }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Catégorie --}}
                    @if (in_array('categorie_id', $carte['champs']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Catégorie</label>
                            <select name="categorie_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                <option value="">Toutes les catégories</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}">{{ $c->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- État --}}
                    @if (in_array('etat', $carte['champs']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">État</label>
                            <select name="etat" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                <option value="">Tous les états</option>
                                @foreach (config('intendance.etats_bien') as $k => $l)
                                    <option value="{{ $k }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Type d'incident --}}
                    @if (in_array('type_incident_id', $carte['champs']))
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 mb-1">Type d'incident</label>
                            <select name="type_incident_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                <option value="">Tous les types</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}">{{ $t->nom }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    {{-- Statut et Priorité --}}
                    @if (in_array('statut', $carte['champs']))
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Statut</label>
                                <select name="statut" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                    <option value="">Tous</option>
                                    @foreach (config('intendance.statuts_incident') as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Priorité</label>
                                <select name="priorite" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                                    <option value="">Toutes</option>
                                    @foreach (config('intendance.priorites') as $k => $l)
                                        <option value="{{ $k }}">{{ $l }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    {{-- Boutons d'export (poussés en bas) --}}
                    <div class="flex gap-2 pt-4 mt-auto border-t border-slate-100">
                        <button name="format" value="pdf" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark py-2.5 text-sm font-semibold text-white hover:bg-slate-800 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                            PDF
                        </button>
                        <button name="format" value="csv" class="flex-1 inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                            <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Excel (CSV)
                        </button>
                    </div>
                </form>
            </div>
        @endforeach
    </div>
</div>
@endsection