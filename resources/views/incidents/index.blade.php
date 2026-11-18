@extends('layouts.app')

@section('title', auth()->user()->isMaintenance() ? 'Mes interventions' : 'Incidents')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ auth()->user()->isMaintenance() ? 'Mes interventions' : 'Gestion des incidents' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                {{ auth()->user()->isMaintenance() ? 'Liste des incidents qui vous sont assignés.' : 'Suivez et traitez les incidents signalés dans l\'Hôtel de Ville.' }}
            </p>
        </div>

        @unless (auth()->user()->isMaintenance())
            <a href="{{ route('incidents.create') }}" 
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-red px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Signaler un incident
            </a>
        @endunless
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 items-end text-sm">
            {{-- Recherche --}}
            <div class="lg:col-span-2">
                <label for="q" class="block text-xs font-semibold text-slate-500 mb-1">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Référence ou texte..." 
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
                </div>
            </div>

            {{-- Statut --}}
            <div>
                <label for="statut" class="block text-xs font-semibold text-slate-500 mb-1">Statut</label>
                <select id="statut" name="statut" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous</option>
                    @foreach (config('intendance.statuts_incident') as $k => $l)
                        <option value="{{ $k }}" @selected(request('statut') == $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Priorité --}}
            <div>
                <label for="priorite" class="block text-xs font-semibold text-slate-500 mb-1">Priorité</label>
                <select id="priorite" name="priorite" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Toutes</option>
                    @foreach (config('intendance.priorites') as $k => $l)
                        <option value="{{ $k }}" @selected(request('priorite') == $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div>
                <label for="type_incident_id" class="block text-xs font-semibold text-slate-500 mb-1">Type</label>
                <select id="type_incident_id" name="type_incident_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous</option>
                    @foreach ($types as $t)
                        <option value="{{ $t->id }}" @selected(request('type_incident_id') == $t->id)>{{ $t->nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Étage (Admin seulement) --}}
            @if (auth()->user()->isAdmin())
                <div>
                    <label for="etage_id" class="block text-xs font-semibold text-slate-500 mb-1">Étage</label>
                    <select id="etage_id" name="etage_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                        <option value="">Tous</option>
                        @foreach ($etages as $e)
                            <option value="{{ $e->id }}" @selected(request('etage_id') == $e->id)>{{ $e->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="bureau_id" class="block text-xs font-semibold text-slate-500 mb-1">Bureau</label>
                    <select id="bureau_id" name="bureau_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                        <option value="">Tous</option>
                        @foreach ($bureaux as $b)
                            <option value="{{ $b->id }}" @selected(request('bureau_id') == $b->id)>{{ $b->numero }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            {{-- Dates --}}
            <div class="lg:col-span-2 flex gap-2">
                <div class="flex-1">
                    <label for="du" class="block text-xs font-semibold text-slate-500 mb-1">Du</label>
                    <input type="date" id="du" name="du" value="{{ request('du') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                </div>
                <div class="flex-1">
                    <label for="au" class="block text-xs font-semibold text-slate-500 mb-1">Au</label>
                    <input type="date" id="au" name="au" value="{{ request('au') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                </div>
            </div>

            {{-- Bouton Filtrer --}}
            <div class="lg:col-span-1">
                <button type="submit" class="w-full h-[38px] rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition-colors border border-slate-300 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Tableau des incidents --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Référence</th>
                        <th class="px-6 py-4">Date</th>
                        <th class="px-6 py-4">Bureau</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Priorité</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4">Déclarant</th>
                        <th class="px-6 py-4">Assigné à</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($incidents as $i)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            <td class="px-6 py-4">
                                <a class="inline-flex items-center font-mono text-xs font-bold text-brand-blue bg-blue-50 px-2 py-1 rounded-md border border-blue-100 hover:bg-blue-100 transition-colors" href="{{ route('incidents.show', $i) }}">
                                    {{ $i->reference }}
                                </a>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-slate-600 text-xs">
                                <div class="flex flex-col">
                                    <span>{{ $i->created_at->format('d/m/Y') }}</span>
                                    <span class="text-slate-400">{{ $i->created_at->format('H:i') }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-mono text-xs font-bold border border-slate-200">
                                        {{ $i->bureau->numero }}
                                    </span>
                                    <span class="text-slate-500 text-xs">{{ $i->bureau->etage->nom ?? '' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $i->type->nom }}
                            </td>
                            <td class="px-6 py-4">
                                @include('partials.badge', ['type' => 'priorite', 'value' => $i->priorite])
                            </td>
                            <td class="px-6 py-4">
                                @include('partials.badge', ['type' => 'statut', 'value' => $i->statut])
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                                        {{ substr($i->declarant->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-slate-600 text-xs truncate max-w-[100px]" title="{{ $i->declarant->name ?? '' }}">{{ $i->declarant->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($i->assigne)
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-brand-blue/10 flex items-center justify-center text-[10px] font-bold text-brand-blue">
                                            {{ substr($i->assigne->name, 0, 1) }}
                                        </div>
                                        <span class="text-slate-600 text-xs truncate max-w-[100px]" title="{{ $i->assigne->name }}">{{ $i->assigne->name }}</span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 text-xs text-slate-400 italic">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                        Non assigné
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                <p class="font-medium text-slate-600">Aucun incident trouvé</p>
                                <p class="text-sm mt-1">Essayez de modifier vos filtres ou créez un nouveau signalement.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($incidents->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $incidents->links() }}
            </div>
        @endif
    </div>
</div>
@endsection