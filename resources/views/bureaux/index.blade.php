@extends('layouts.app')

@section('title', $archives ? 'Bureaux archivés' : 'Bureaux')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête de la page --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ $archives ? 'Bureaux archivés' : 'Bureaux de l\'Hôtel de Ville' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                {{ $archives ? 'Liste des bureaux désactivés. Vous pouvez les restaurer à tout moment.' : 'Gérez les espaces, les agents affectés et les QR codes d\'identification.' }}
            </p>
        </div>
        
        @if (auth()->user()->isAdmin())
        <div class="flex flex-wrap items-center gap-3">
            {{-- Bouton Archives / Actifs --}}
            <a href="{{ route('bureaux.index', ['archives' => $archives ? 0 : 1]) }}" 
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                @if($archives)
                    <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                    Bureaux actifs
                @else
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archives
                @endif
            </a>

            {{-- Bouton Planche QR --}}
            <a href="{{ route('bureaux.planche', request()->only('etage_id')) }}" 
               class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm" title="Générer une planche PDF de QR codes">
                <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                Planche QR
            </a>
            
            {{-- Bouton Nouveau --}}
            <a href="{{ route('bureaux.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Nouveau bureau
            </a>
        </div>
        @endif
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 items-end text-sm">
            {{-- Recherche --}}
            <div>
                <label for="q" class="block text-xs font-semibold text-slate-500 mb-1">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="N° ou libellé..." 
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
                </div>
            </div>

            {{-- Étage --}}
            <div>
                <label for="etage_id" class="block text-xs font-semibold text-slate-500 mb-1">Étage</label>
                <select id="etage_id" name="etage_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les étages</option>
                    @foreach ($etages as $e)
                        <option value="{{ $e->id }}" @selected(request('etage_id') == $e->id)>{{ $e->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Service --}}
            <div>
                <label for="service" class="block text-xs font-semibold text-slate-500 mb-1">Service</label>
                <input id="service" name="service" value="{{ request('service') }}" placeholder="Nom du service..." 
                       class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
            </div>

            {{-- Bouton filtre + hidden --}}
            <div class="flex items-end gap-2">
                @if ($archives)
                    <input type="hidden" name="archives" value="1">
                @endif
                <button type="submit" class="h-[38px] w-full rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition-colors border border-slate-300 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Tableau des bureaux --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">N°</th>
                        <th class="px-6 py-4">Libellé</th>
                        <th class="px-6 py-4">Étage</th>
                        <th class="px-6 py-4">Service</th>
                        <th class="px-6 py-4 text-center">Biens</th>
                        <th class="px-6 py-4 text-center">Incidents ouverts</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($bureaux as $b)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            {{-- Numéro --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                                    {{ $b->numero }}
                                </span>
                            </td>
                            
                            {{-- Libellé --}}
                            <td class="px-6 py-4 font-medium text-slate-800 group-hover:text-brand-blue transition-colors">
                                {{ $b->libelle }}
                            </td>
                            
                            {{-- Étage --}}
                            <td class="px-6 py-4 text-slate-600">
                                <div class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    {{ $b->etage->nom }}
                                </div>
                            </td>
                            
                            {{-- Service --}}
                            <td class="px-6 py-4 text-slate-500 text-xs">
                                {{ $b->service ?: '—' }}
                            </td>
                            
                            {{-- Biens (Badge) --}}
                            <td class="px-6 py-4 text-center">
                                @if($b->biens_count > 0)
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-2 py-0.5 rounded-full bg-blue-50 text-brand-blue text-xs font-bold border border-blue-100">
                                        {{ $b->biens_count }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            
                            {{-- Incidents ouverts (Badge conditionnel) --}}
                            <td class="px-6 py-4 text-center">
                                @if($b->incidents_ouverts_count > 0)
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-2 py-0.5 rounded-full bg-red-50 text-brand-red text-xs font-bold border border-red-100 shadow-[0_0_8px_rgba(206,16,33,0.15)] animate-pulse-slow">
                                        {{ $b->incidents_ouverts_count }}
                                    </span>
                                @else
                                    <span class="text-slate-300">—</span>
                                @endif
                            </td>
                            
                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                @if ($archives)
                                    <form method="POST" action="{{ route('bureaux.restore', $b->id) }}" class="inline-block">
                                        @csrf
                                        <button class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Restaurer
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('bureaux.show', $b) }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-brand-blue hover:text-brand-dark transition-colors">
                                        Ouvrir
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <p class="font-medium text-slate-600">Aucun bureau trouvé</p>
                                <p class="text-sm mt-1">Essayez de modifier vos filtres ou d'ajouter un nouveau bureau.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($bureaux->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $bureaux->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    /* Petite animation pour les badges d'incidents ouverts */
    @keyframes pulse-slow {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }
    .animate-pulse-slow {
        animation: pulse-slow 3s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }
</style>
@endpush