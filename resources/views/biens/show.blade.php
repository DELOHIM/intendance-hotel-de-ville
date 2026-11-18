@extends('layouts.app')

@section('title', $bien->designation)

@section('content')
<div class="max-w-6xl mx-auto">
    
    {{-- En-tête : Titre et Bouton Retour --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('biens.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-brand-blue hover:border-brand-blue transition-colors shadow-sm" title="Retour à l'inventaire">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="font-heading text-2xl font-bold text-slate-800">{{ $bien->designation }}</h1>
                    @include('partials.badge', ['type' => 'etat', 'value' => $bien->etat])
                </div>
                <p class="text-sm text-slate-500 mt-1">{{ $bien->categorie->nom }} · N° {{ $bien->numero_serie ?: 'Non renseigné' }}</p>
            </div>
        </div>
        
        {{-- Actions principales --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('biens.edit', $bien) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <form method="POST" action="{{ route('biens.destroy', $bien) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce bien ? Cette action est irréversible.')">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-2 rounded-xl border border-red-200 bg-red-50 px-4 py-2 text-sm font-semibold text-brand-red hover:bg-red-100 transition-colors shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Supprimer
                </button>
            </form>
        </div>
    </div>

    {{-- Grille Principale --}}
    <div class="grid gap-6 lg:grid-cols-3">
        
        {{-- Colonne de gauche (Détails) --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Carte d'informations --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Détails du bien
                </h2>
                
                <dl class="grid gap-6 sm:grid-cols-2 text-sm">
                    {{-- Bureau --}}
                    <div>
                        <dt class="text-slate-500 mb-1">Bureau d'affectation</dt>
                        <dd>
                            <a href="{{ route('bureaux.show', $bien->bureau) }}" class="inline-flex items-center gap-2 font-medium text-brand-blue hover:text-brand-dark transition-colors bg-blue-50/50 px-2 py-1 rounded-md border border-blue-100">
                                <span class="font-mono text-xs bg-white px-1.5 py-0.5 rounded shadow-sm border border-blue-200">{{ $bien->bureau->numero }}</span>
                                {{ $bien->bureau->libelle }} 
                                <span class="text-slate-400 text-xs">({{ $bien->bureau->etage->nom ?? 'N/A' }})</span>
                            </a>
                        </dd>
                    </div>

                    {{-- Date d'acquisition --}}
                    <div>
                        <dt class="text-slate-500 mb-1">Date d'acquisition</dt>
                        <dd class="font-medium text-slate-800 flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            {{ optional($bien->date_acquisition)->format('d/m/Y') ?: 'Non renseignée' }}
                        </dd>
                    </div>

                    {{-- Remarques --}}
                    <div class="sm:col-span-2 pt-4 border-t border-slate-100">
                        <dt class="text-slate-500 mb-2">Remarques</dt>
                        <dd class="text-slate-700 bg-slate-50 p-3 rounded-lg border border-slate-100 italic">
                            {{ $bien->remarques ?: 'Aucune remarque particulière.' }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- Section Historique --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Historique du bien
                </h2>
                
                <div class="relative border-l border-slate-200 ml-3 space-y-6">
                    @forelse ($bien->historiques as $h)
                        <div class="relative pl-6">
                            {{-- Point de la timeline --}}
                            <span class="absolute -left-[5px] top-1.5 h-2.5 w-2.5 rounded-full bg-brand-blue ring-4 ring-white"></span>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1">
                                <div class="font-semibold text-slate-800 capitalize flex items-center gap-2">
                                    {{ $h->action }}
                                    @if ($h->action === 'transfert')
                                        <span class="text-xs font-normal text-slate-500 bg-slate-100 px-2 py-0.5 rounded-full border border-slate-200">
                                            {{ $h->ancienBureau->numero ?? '?' }} → {{ $h->nouveauBureau->numero ?? '?' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ $h->created_at->format('d/m/Y à H:i') }} · <span class="font-medium text-slate-500">{{ $h->user->name ?? 'Système' }}</span>
                                </div>
                            </div>
                            @if ($h->details)
                                <div class="text-sm text-slate-600 mt-1">{{ $h->details }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="pl-6 text-sm text-slate-500 py-2">Aucun historique pour ce bien.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne de droite (Actions & Incidents) --}}
        <div class="space-y-6">
            
            {{-- Carte de transfert --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-blue/10 rounded-lg text-brand-blue">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <h2 class="font-heading font-semibold text-slate-800">Transférer</h2>
                </div>
                
                <form method="POST" action="{{ route('biens.transfer', $bien) }}" class="space-y-4 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Bureau de destination <span class="text-brand-red">*</span></label>
                        <select name="bureau_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                            <option value="">— Choisir —</option>
                            @foreach ($bureaux as $b)
                                @if ($b->id !== $bien->bureau_id)
                                    <option value="{{ $b->id }}">{{ $b->numero }} — {{ $b->libelle }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 mb-1">Motif du transfert</label>
                        <input name="motif" placeholder="Optionnel..." class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    </div>
                    <button class="w-full flex justify-center items-center gap-2 rounded-xl bg-slate-800 px-4 py-2.5 font-semibold text-white hover:bg-slate-900 transition-colors shadow-sm">
                        Confirmer le transfert
                    </button>
                </form>
            </div>

            {{-- Carte des incidents --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-brand-red/10 rounded-lg text-brand-red">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <h2 class="font-heading font-semibold text-slate-800">Incidents liés</h2>
                </div>
                
                <div class="space-y-3">
                    @forelse ($incidents as $i)
                        <a href="{{ route('incidents.show', $i) }}" class="block p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:bg-white hover:border-slate-300 hover:shadow-sm transition-all group">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-mono text-xs font-bold text-slate-500 group-hover:text-brand-blue">{{ $i->reference }}</span>
                                @include('partials.badge', ['type' => 'statut', 'value' => $i->statut])
                            </div>
                            <div class="text-sm font-medium text-slate-800 mb-1">{{ $i->type->nom }}</div>
                            <div class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ $i->created_at->format('d/m/Y') }}
                            </div>
                        </a>
                    @empty
                        <div class="text-center py-6 text-sm text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            Aucun incident signalé<br>pour ce bien.
                        </div>
                    @endforelse
                </div>
            </div>
            
        </div>
    </div>
</div>
@endsection