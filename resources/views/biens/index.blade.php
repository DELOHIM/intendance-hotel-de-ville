@extends('layouts.app')

@section('title', 'Inventaire des biens')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête de la page --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">Inventaire des biens</h1>
            <p class="text-slate-500 mt-1 text-sm">Gérez et suivez l'ensemble des équipements et mobiliers de l'Hôtel de Ville.</p>
        </div>
        <div class="flex items-center gap-3">
            {{-- Boutons d'exportation --}}
            <div class="flex rounded-xl shadow-sm border border-slate-300 overflow-hidden">
                <a href="{{ route('biens.export', array_merge(request()->query(), ['format' => 'csv'])) }}" 
                   class="flex items-center gap-1.5 px-3 py-2 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors border-r border-slate-300"
                   title="Exporter en Excel">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    CSV
                </a>
                <a href="{{ route('biens.export', array_merge(request()->query(), ['format' => 'pdf'])) }}" 
                   class="flex items-center gap-1.5 px-3 py-2 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-colors"
                   title="Exporter en PDF">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    PDF
                </a>
            </div>
            
            <a href="{{ route('biens.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau bien
            </a>
        </div>
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end text-sm">
            {{-- Recherche texte --}}
            <div>
                <label for="q" class="block text-xs font-semibold text-slate-500 mb-1">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Nom ou n° série..." 
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
                </div>
            </div>

            {{-- Filtre étage --}}
            <div>
                <label for="etage_id" class="block text-xs font-semibold text-slate-500 mb-1">Étage</label>
                <select id="etage_id" name="etage_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les étages</option>
                    @foreach ($etages as $e)
                        <option value="{{ $e->id }}" @selected(request('etage_id') == $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre bureau --}}
            <div>
                <label for="bureau_id" class="block text-xs font-semibold text-slate-500 mb-1">Bureau</label>
                <select id="bureau_id" name="bureau_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les bureaux</option>
                    @foreach ($bureaux as $b)
                        <option value="{{ $b->id }}" @selected(request('bureau_id') == $b->id)>{{ $b->numero }} — {{ $b->libelle }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre catégorie --}}
            <div>
                <label for="categorie_id" class="block text-xs font-semibold text-slate-500 mb-1">Catégorie</label>
                <select id="categorie_id" name="categorie_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Toutes catégories</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(request('categorie_id') == $c->id)>{{ $c->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Filtre état et bouton --}}
            <div class="flex gap-2">
                <div class="flex-1">
                    <label for="etat" class="block text-xs font-semibold text-slate-500 mb-1">État</label>
                    <select id="etat" name="etat" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                        <option value="">Tous états</option>
                        @foreach (config('intendance.etats_bien') as $k => $l)
                            <option value="{{ $k }}" @selected(request('etat') == $k)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="h-[38px] px-4 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition-colors border border-slate-300" title="Appliquer les filtres">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Tableau des biens --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Désignation</th>
                        <th class="px-6 py-4">Catégorie</th>
                        <th class="px-6 py-4">N° série</th>
                        <th class="px-6 py-4">Bureau</th>
                        <th class="px-6 py-4">État</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($biens as $b)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            <td class="px-6 py-4">
                                <a class="font-medium text-slate-800 group-hover:text-brand-blue transition-colors" href="{{ route('biens.show', $b) }}">
                                    {{ $b->designation }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-slate-600">{{ $b->categorie->nom }}</td>
                            <td class="px-6 py-4 text-slate-500 font-mono text-xs">{{ $b->numero_serie ?? '—' }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-600 font-mono text-xs font-semibold">
                                        {{ $b->bureau->numero }}
                                    </span>
                                    <span class="text-slate-500 text-xs">{{ $b->bureau->etage->nom ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @include('partials.badge', ['type' => 'etat', 'value' => $b->etat])
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                <p class="font-medium text-slate-600">Aucun bien trouvé</p>
                                <p class="text-sm mt-1">Essayez de modifier vos filtres ou d'ajouter un nouveau bien.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($biens->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $biens->links() }}
            </div>
        @endif
    </div>
</div>
@endsection