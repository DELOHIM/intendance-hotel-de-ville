@extends('layouts.app')

@section('title', 'Journal d\'activité')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Journal d'activité</h1>
        <p class="text-slate-500 mt-1 text-sm">Historique de toutes les actions sensibles effectuées sur la plateforme (audit trail).</p>
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 items-end text-sm">
            
            {{-- Utilisateur --}}
            <div>
                <label for="user_id" class="block text-xs font-semibold text-slate-500 mb-1">Utilisateur</label>
                <select id="user_id" name="user_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les utilisateurs</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Action --}}
            <div>
                <label for="action" class="block text-xs font-semibold text-slate-500 mb-1">Type d'action</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="action" name="action" value="{{ request('action') }}" placeholder="ex: bureau, incident..." 
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
                </div>
            </div>

            {{-- Du --}}
            <div>
                <label for="du" class="block text-xs font-semibold text-slate-500 mb-1">Du</label>
                <input type="date" id="du" name="du" value="{{ request('du') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
            </div>

            {{-- Au --}}
            <div>
                <label for="au" class="block text-xs font-semibold text-slate-500 mb-1">Au</label>
                <input type="date" id="au" name="au" value="{{ request('au') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
            </div>

            {{-- Bouton Filtrer --}}
            <div>
                <button type="submit" class="w-full h-[38px] rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition-colors border border-slate-300 flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Date & Heure</th>
                        <th class="px-6 py-4">Utilisateur</th>
                        <th class="px-6 py-4">Action</th>
                        <th class="px-6 py-4">Description</th>
                        <th class="px-6 py-4 text-right">Adresse IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $l)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            
                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-700">{{ $l->created_at->format('d/m/Y') }}</span>
                                    <span class="text-slate-400 font-mono mt-0.5">{{ $l->created_at->format('H:i:s') }}</span>
                                </div>
                            </td>
                            
                            {{-- Utilisateur --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600 shrink-0">
                                        {{ substr($l->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    <span class="font-medium text-slate-800 text-sm truncate max-w-[150px]">{{ $l->user->name ?? 'Système' }}</span>
                                </div>
                            </td>
                            
                            {{-- Action --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600 font-mono text-xs border border-slate-200 shadow-sm">
                                    {{ $l->action }}
                                </span>
                            </td>
                            
                            {{-- Description --}}
                            <td class="px-6 py-4 text-slate-600 text-sm max-w-md truncate" title="{{ $l->description }}">
                                {{ $l->description }}
                            </td>
                            
                            {{-- IP --}}
                            <td class="px-6 py-4 text-right">
                                <span class="inline-flex items-center gap-1 text-xs font-mono text-slate-400 group-hover:text-slate-500 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                    {{ $l->ip }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                <p class="font-medium text-slate-600">Aucune activité trouvée</p>
                                <p class="text-sm mt-1">Essayez de modifier vos critères de recherche.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection