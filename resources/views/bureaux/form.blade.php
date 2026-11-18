@extends('layouts.app')

@section('title', $bureau->exists ? 'Modifier le bureau '.$bureau->numero : 'Nouveau bureau')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ $bureau->exists ? 'Modifier le bureau '.$bureau->numero : 'Créer un nouveau bureau' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                {{ $bureau->exists ? 'Mettez à jour les informations de ce bureau.' : 'Renseignez les informations pour enregistrer un nouveau bureau dans l\'inventaire.' }}
            </p>
        </div>
        <a href="{{ $bureau->exists ? route('bureaux.show', $bureau) : route('bureaux.index') }}" 
           class="hidden sm:inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
    </div>

    {{-- Carte du formulaire --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ $bureau->exists ? route('bureaux.update', $bureau) : route('bureaux.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf 
            @if ($bureau->exists) @method('PUT') @endif

            {{-- Section 1 : Identification --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    Identification
                </h3>
                
                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- Numéro --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="numero">Numéro unique <span class="text-brand-red">*</span></label>
                        @if ($bureau->exists)
                            <input value="{{ $bureau->numero }}" disabled
                                   class="w-full rounded-xl border border-slate-200 bg-slate-50 px-4 py-2.5 text-slate-500 font-mono text-sm shadow-inner cursor-not-allowed">
                            <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Le numéro est généré lors de la création et ne peut plus être modifié.
                            </p>
                        @else
                            <input id="numero" name="numero" value="{{ old('numero') }}" required placeholder="ex: B-101"
                                   class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm font-mono uppercase">
                            @error('numero')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                        @endif
                    </div>

                    {{-- Étage --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="etage_id">Étage <span class="text-brand-red">*</span></label>
                        <select id="etage_id" name="etage_id" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                            <option value="">— Choisir un étage —</option>
                            @foreach ($etages as $e)
                                <option value="{{ $e->id }}" @selected(old('etage_id', $bureau->etage_id) == $e->id)>{{ $e->nom }}</option>
                            @endforeach
                        </select>
                        @error('etage_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 2 : Description --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informations du bureau
                </h3>
                
                <div class="space-y-5">
                    {{-- Libellé --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="libelle">Libellé <span class="text-brand-red">*</span></label>
                        <input id="libelle" name="libelle" value="{{ old('libelle', $bureau->libelle) }}" required 
                               placeholder="Ex: Bureau du Directeur Général, Salle de réunion..."
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        @error('libelle')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Service --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="service">Service / Direction</label>
                        <input id="service" name="service" value="{{ old('service', $bureau->service) }}" 
                               placeholder="Ex: Direction des Ressources Humaines"
                               class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        @error('service')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="description">Description / Localisation précise</label>
                        <textarea id="description" name="description" rows="3" 
                                  placeholder="Ex: Situé au bout du couloir, à côté de la salle de conférence..."
                                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">{{ old('description', $bureau->description) }}</textarea>
                        @error('description')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 3 : Agents --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    Agents responsables
                </h3>
                
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="agents">Affecter un ou plusieurs agents</label>
                    <select id="agents" name="agents[]" multiple size="6" 
                            class="w-full rounded-xl border border-slate-300 px-4 py-2 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        @foreach ($agents as $a)
                            <option value="{{ $a->id }}" @selected(in_array($a->id, old('agents', $affectes)))>
                                {{ $a->name }} ({{ $a->roleLabel() }})
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-slate-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Maintenez la touche <kbd class="bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded text-[10px] font-mono font-bold">Ctrl</kbd> (ou <kbd class="bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded text-[10px] font-mono font-bold">Cmd</kbd> sur Mac) pour sélectionner plusieurs agents.
                    </p>
                </div>
            </div>

            {{-- Actions --}}
            <div class="pt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ $bureau->exists ? route('bureaux.show', $bureau) : route('bureaux.index') }}" 
                   class="w-full sm:w-auto text-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $bureau->exists ? 'Mettre à jour le bureau' : 'Créer le bureau' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection