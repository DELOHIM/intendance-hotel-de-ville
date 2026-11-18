@extends('layouts.app')

@section('title', $bien->exists ? 'Modifier le bien' : 'Nouveau bien')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- En-tête de la page --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ $bien->exists ? 'Modifier le bien' : 'Enregistrer un nouveau bien' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">Renseignez les informations relatives au bien pour l'inventaire.</p>
        </div>
        <a href="{{ $bien->exists ? route('biens.show', $bien) : route('biens.index') }}" 
           class="hidden sm:inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
    </div>

    {{-- Carte du formulaire --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ $bien->exists ? route('biens.update', $bien) : route('biens.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf 
            @if ($bien->exists) @method('PUT') @endif

            {{-- Section 1 : Informations générales --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informations générales
                </h3>
                
                <div class="space-y-5">
                    {{-- Désignation --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="designation">Désignation <span class="text-brand-red">*</span></label>
                        <input id="designation" name="designation" value="{{ old('designation', $bien->designation) }}" required 
                               placeholder="Ex: Climatiseur split system, Bureau en bois..."
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        @error('designation')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        {{-- Catégorie --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="categorie_id">Catégorie <span class="text-brand-red">*</span></label>
                            <select id="categorie_id" name="categorie_id" required 
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                                <option value="">— Choisir une catégorie —</option>
                                @foreach ($categories as $c)
                                    <option value="{{ $c->id }}" @selected(old('categorie_id', $bien->categorie_id) == $c->id)>{{ $c->nom }}</option>
                                @endforeach
                            </select>
                            @error('categorie_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                        </div>

                        {{-- Numéro de série --}}
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="numero_serie">N° de série / inventaire</label>
                            <input id="numero_serie" name="numero_serie" value="{{ old('numero_serie', $bien->numero_serie) }}" 
                                   placeholder="Ex: INV-2026-001"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                            @error('numero_serie')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 2 : Statut & Acquisition --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    Statut & Acquisition
                </h3>
                
                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- État --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="etat">État actuel</label>
                        <select id="etat" name="etat" 
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                            @foreach (config('intendance.etats_bien') as $k => $l)
                                <option value="{{ $k }}" @selected(old('etat', $bien->etat) === $k)>{{ $l }}</option>
                            @endforeach
                        </select>
                        @error('etat')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Date d'acquisition --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="date_acquisition">Date d'acquisition</label>
                        <input id="date_acquisition" type="date" name="date_acquisition" 
                               value="{{ old('date_acquisition', optional($bien->date_acquisition)->format('Y-m-d')) }}" 
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        @error('date_acquisition')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 3 : Affectation --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Affectation
                </h3>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="bureau_id">Bureau d'affectation <span class="text-brand-red">*</span></label>
                    <select id="bureau_id" name="bureau_id" required 
                            class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        <option value="">— Choisir un bureau —</option>
                        @foreach ($bureaux as $b)
                            <option value="{{ $b->id }}" @selected(old('bureau_id', $bien->bureau_id) == $b->id)>
                                {{ $b->numero }} — {{ $b->libelle }} ({{ $b->etage->nom ?? '' }})
                            </option>
                        @endforeach
                    </select>
                    @error('bureau_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 4 : Remarques --}}
            <div>
                <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="remarques">Remarques (optionnel)</label>
                <textarea id="remarques" name="remarques" rows="3" 
                          placeholder="Ajoutez des détails supplémentaires sur l'état du bien, son emplacement précis, etc."
                          class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">{{ old('remarques', $bien->remarques) }}</textarea>
                @error('remarques')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
            </div>

            {{-- Actions --}}
            <div class="pt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ $bien->exists ? route('biens.show', $bien) : route('biens.index') }}" 
                   class="w-full sm:w-auto text-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $bien->exists ? 'Mettre à jour le bien' : 'Enregistrer le bien' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection