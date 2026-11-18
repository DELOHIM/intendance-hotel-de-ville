@extends('layouts.app')

@section('title', $maintenance ? 'Mes interventions' : 'Mon espace')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">

    {{-- En-tête de bienvenue --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ $maintenance ? 'Mes interventions' : 'Mon espace' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                {{ $maintenance ? 'Voici la liste des incidents qui vous sont assignés.' : 'Retrouvez ici vos signalements et vos bureaux affectés.' }}
            </p>
        </div>
        
        @unless ($maintenance)
            <a href="{{ route('incidents.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-red px-5 py-3 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Signaler un incident
            </a>
        @endunless
    </div>

    {{-- Cartes de statistiques --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
        {{-- Carte "À traiter" ou "Mes incidents ouverts" --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 rounded-full bg-amber-500/5 group-hover:bg-amber-500/10 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">{{ $maintenance ? 'À traiter' : 'Mes incidents ouverts' }}</p>
                <p class="text-3xl font-bold text-amber-600">{{ $compteurs['a_traiter'] }}</p>
            </div>
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600 relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        {{-- Carte "Résolus" --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 rounded-full bg-green-500/5 group-hover:bg-green-500/10 transition-colors"></div>
            <div class="relative z-10">
                <p class="text-sm font-medium text-slate-500 mb-1">Résolus</p>
                <p class="text-3xl font-bold text-green-600">{{ $compteurs['resolus'] }}</p>
            </div>
            <div class="p-3 rounded-xl bg-green-50 text-green-600 relative z-10">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
    </div>

    {{-- Mes bureaux (non visible pour la maintenance) --}}
    @unless ($maintenance)
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <h2 class="font-heading font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Mes bureaux affectés
                </h2>
                <span class="bg-slate-200 text-slate-600 text-xs px-2 py-0.5 rounded-full">{{ $bureaux->count() }}</span>
            </div>
            
            <div class="p-5">
                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($bureaux as $b)
                        <a href="{{ route('bureaux.show', $b) }}" class="group block p-4 rounded-xl border border-slate-200 bg-white hover:border-brand-blue hover:shadow-sm transition-all relative overflow-hidden">
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-brand-blue opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                                    {{ $b->numero }}
                                </span>
                                <svg class="w-4 h-4 text-slate-300 group-hover:text-brand-blue transition-colors transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </div>
                            <h3 class="font-semibold text-slate-800 group-hover:text-brand-blue transition-colors text-sm">{{ $b->libelle }}</h3>
                            <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $b->etage->nom }}
                            </p>
                        </a>
                    @empty
                        <div class="col-span-full text-center py-8">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-50 text-slate-400 mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                            <p class="text-sm font-medium text-slate-700">Aucun bureau affecté</p>
                            <p class="text-xs text-slate-500 mt-1 mb-3">Vous n'avez pas encore de bureau assigné.</p>
                            <a href="{{ route('bureaux.index') }}" class="inline-flex items-center gap-1 text-sm font-medium text-brand-blue hover:text-brand-dark transition-colors">
                                Parcourir les bureaux
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endunless

    {{-- Derniers signalements / Interventions --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="font-heading font-semibold text-slate-800 flex items-center gap-2">
                @if($maintenance)
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Interventions assignées
                @else
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                    Mes derniers signalements
                @endif
            </h2>
            <a href="{{ route('incidents.index') }}" class="text-xs font-semibold text-brand-blue bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">Tout voir</a>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse ($incidents as $i)
                <a href="{{ route('incidents.show', $i) }}" class="flex flex-col sm:flex-row sm:items-center justify-between p-4 hover:bg-slate-50 transition-colors group gap-3">
                    <div class="flex items-center gap-3">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded-md shadow-sm shrink-0">
                            {{ $i->reference }}
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-blue transition-colors">
                                {{ $i->type->nom ?? 'Incident' }}
                            </p>
                            <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                Bureau {{ $i->bureau->numero ?? 'N/A' }} · {{ $i->created_at->format('d/m/Y') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 sm:justify-end">
                        @include('partials.badge', ['type' => 'priorite', 'value' => $i->priorite])
                        @include('partials.badge', ['type' => 'statut', 'value' => $i->statut])
                    </div>
                </a>
            @empty
                <div class="text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                    </div>
                    <p class="text-sm font-medium text-slate-700">Aucun incident pour le moment</p>
                    <p class="text-xs text-slate-500 mt-1">
                        {{ $maintenance ? 'Vous n\'avez aucune intervention assignée.' : 'Vous n\'avez pas encore signalé d\'incident.' }}
                    </p>
                </div>
            @endforelse
        </div>
    </div>

</div>
@endsection