@extends('layouts.app')

@section('title', 'Signaler un incident')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Signaler un incident</h1>
        <p class="text-slate-500 mt-1 text-sm">Décrivez le problème rencontré. Les administrateurs seront notifiés automatiquement par WhatsApp.</p>
    </div>

    {{-- Carte du formulaire --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ route('incidents.store') }}" enctype="multipart/form-data"
              x-data="{ 
                  bureau: '{{ old('bureau_id', $bureauId) }}', 
                  biens: @js($biens), 
                  bien: '{{ old('bien_id') }}' 
              }" 
              class="p-6 sm:p-8 space-y-6">
            @csrf

            {{-- Section 1 : Localisation --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Localisation
                </h3>
                
                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- Bureau --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="bureau_id">Bureau concerné <span class="text-brand-red">*</span></label>
                        <select id="bureau_id" name="bureau_id" x-model="bureau" @change="bien = ''" required 
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                            <option value="">— Choisir le bureau —</option>
                            @foreach ($bureaux as $b)
                                <option value="{{ $b->id }}">{{ $b->numero }} — {{ $b->libelle }} ({{ $b->etage->nom ?? '' }})</option>
                            @endforeach
                        </select>
                        @error('bureau_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Bien --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="bien_id">Bien concerné <span class="text-slate-400 font-normal text-xs ml-1">(optionnel)</span></label>
                        <select id="bien_id" name="bien_id" x-model="bien" 
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                            <option value="">— Aucun bien précis —</option>
                            <template x-for="b in biens.filter(x => String(x.bureau_id) === String(bureau))" :key="b.id">
                                <option :value="b.id" x-text="b.designation + (b.numero_serie ? ' (' + b.numero_serie + ')' : '')"></option>
                            </template>
                        </select>
                        <p class="mt-1.5 text-xs text-slate-500" x-show="bureau && biens.filter(x => String(x.bureau_id) === String(bureau)).length === 0">
                            Aucun bien enregistré dans ce bureau.
                        </p>
                        @error('bien_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 2 : Détails du problème --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Détails du problème
                </h3>
                
                <div class="space-y-5">
                    {{-- Type & Priorité --}}
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="type_incident_id">Type de problème <span class="text-brand-red">*</span></label>
                            <select id="type_incident_id" name="type_incident_id" required 
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                                <option value="">— Choisir un type —</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}" @selected(old('type_incident_id') == $t->id)>{{ $t->nom }}</option>
                                @endforeach
                            </select>
                            @error('type_incident_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="priorite">Niveau de priorité</label>
                            <select id="priorite" name="priorite" 
                                    class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                                @foreach (config('intendance.priorites') as $k => $l)
                                    <option value="{{ $k }}" @selected(old('priorite', 'moyenne') === $k)>{{ $l }}</option>
                                @endforeach
                            </select>
                            @error('priorite')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="description">Description détaillée <span class="text-brand-red">*</span></label>
                        <textarea id="description" name="description" rows="4" required minlength="10" 
                                  placeholder="Décrivez précisément le problème constaté (ex: La climatisation ne refroidit plus et fait un bruit anormal depuis ce matin...)"
                                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">{{ old('description') }}</textarea>
                        <p class="mt-1.5 text-xs text-slate-500">Minimum 10 caractères. Soyez le plus précis possible.</p>
                        @error('description')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 3 : Photo --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Preuve visuelle <span class="text-slate-400 font-normal text-xs ml-1 normal-case tracking-normal">(optionnel)</span>
                </h3>
                
                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-slate-300 border-dashed rounded-xl hover:border-brand-blue transition-colors bg-slate-50/50 cursor-pointer relative group">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400 group-hover:text-brand-blue transition-colors" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-slate-600 justify-center">
                            <label for="photo" class="relative cursor-pointer bg-white rounded-md font-medium text-brand-blue hover:text-brand-dark focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-brand-blue px-2 py-1">
                                <span>Prendre une photo</span>
                                <input id="photo" name="photo" type="file" accept="image/*" capture="environment" class="sr-only">
                            </label>
                            <p class="pl-1 py-1">ou glisser-déposer</p>
                        </div>
                        <p class="text-xs text-slate-500">PNG, JPG jusqu'à 5MB</p>
                    </div>
                </div>
                @error('photo')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
            </div>

            {{-- Alerte WhatsApp --}}
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 flex gap-3">
                <svg class="w-5 h-5 text-brand-blue shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <p class="text-sm text-blue-800">
                    Dès l'envoi de ce formulaire, une <strong>notification WhatsApp automatique</strong> sera envoyée aux administrateurs responsables.
                </p>
            </div>

            {{-- Actions --}}
            <div class="pt-4 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('dashboard') }}" 
                   class="w-full sm:w-auto text-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-red px-5 py-2.5 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Envoyer le signalement
                </button>
            </div>
        </form>
    </div>
</div>
@endsection