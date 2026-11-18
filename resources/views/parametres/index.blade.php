@extends('layouts.app')

@section('title', 'Paramétrage')

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Paramétrage</h1>
        <p class="text-slate-500 mt-1 text-sm">Gérez les données de référence utilisées dans toute l'application.</p>
    </div>

    @php
        $blocs = [
            ['etages', 'Étages du bâtiment', $etages, 'bureaux_count', 'bureau(x)', true, 'brand-blue', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
            ['categories', 'Catégories de biens', $categories, 'biens_count', 'bien(s)', false, 'emerald', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>'],
            ['types', "Types d'incidents", $types, 'incidents_count', 'incident(s)', false, 'amber', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
        ];
    @endphp

    <div class="grid gap-6 lg:grid-cols-3">
        @foreach ($blocs as [$kind, $titre, $items, $compteur, $unite, $avecOrdre, $color, $icon])
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col" x-data="{ edit: null }">
                
                {{-- En-tête du bloc --}}
                <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                    <h2 class="font-heading font-semibold text-slate-800 flex items-center gap-2">
                        <span class="p-1.5 rounded-lg bg-{{ $color }}/10 text-{{ $color }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                        </span>
                        {{ $titre }}
                    </h2>
                    <span class="bg-white text-slate-500 text-xs font-medium px-2 py-1 rounded-md border border-slate-200 shadow-sm">
                        {{ $items->count() }}
                    </span>
                </div>

                <div class="p-5 flex-1 flex flex-col">
                    {{-- Formulaire d'ajout --}}
                    <form method="POST" action="{{ route('parametres.store', $kind) }}" class="mb-4 flex gap-2 text-sm">
                        @csrf
                        <input name="nom" placeholder="Ajouter un nouveau..." required 
                               class="min-w-0 flex-1 rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                        @if ($avecOrdre)
                            <input type="number" name="ordre" placeholder="Ordre" min="0" 
                                   class="w-20 rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white text-center" title="Ordre d'affichage">
                        @endif
                        <button type="submit" class="rounded-xl bg-brand-dark px-3 text-white hover:bg-slate-800 transition-colors shadow-sm flex items-center justify-center shrink-0" title="Ajouter">
                            <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </form>

                    {{-- Liste des éléments --}}
                    <ul class="divide-y divide-slate-100 flex-1">
                        @forelse ($items as $it)
                            <li class="py-2.5">
                                {{-- Mode affichage --}}
                                <div x-show="edit !== {{ $it->id }}" class="flex items-center justify-between gap-3 group">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">{{ $it->nom }}</p>
                                        <p class="text-xs text-slate-400 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            {{ $it->$compteur }} {{ $unite }}
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                        <button type="button" @click="edit = {{ $it->id }}" class="p-1.5 rounded-md text-slate-400 hover:text-brand-blue hover:bg-blue-50 transition-colors" title="Modifier">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        </button>
                                        <form method="POST" action="{{ route('parametres.destroy', [$kind, $it->id]) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-md text-slate-400 hover:text-brand-red hover:bg-red-50 transition-colors" title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                {{-- Mode édition --}}
                                <form x-show="edit === {{ $it->id }}" x-cloak method="POST" action="{{ route('parametres.update', [$kind, $it->id]) }}" class="flex gap-2 items-center">
                                    @csrf @method('PUT')
                                    <input name="nom" value="{{ $it->nom }}" required 
                                           class="min-w-0 flex-1 rounded-lg border border-brand-blue px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/30 bg-white shadow-sm">
                                    @if ($avecOrdre)
                                        <input type="number" name="ordre" value="{{ $it->ordre }}" 
                                               class="w-16 rounded-lg border border-slate-300 px-2 py-1.5 text-sm text-center focus:outline-none focus:ring-2 focus:ring-brand-blue/30">
                                    @endif
                                    <button type="submit" class="p-1.5 rounded-md text-white bg-emerald-500 hover:bg-emerald-600 transition-colors shadow-sm" title="Valider">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button type="button" @click="edit = null" class="p-1.5 rounded-md text-slate-500 bg-slate-100 hover:bg-slate-200 transition-colors" title="Annuler">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </form>
                            </li>
                        @empty
                            <li class="py-6 text-center text-sm text-slate-500">
                                Aucun élément pour le moment.
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection