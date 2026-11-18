@extends('layouts.app')

@section('title', 'Incident '.$incident->reference)

@section('content')
@php($u = auth()->user())

<div class="max-w-6xl mx-auto">
    
    {{-- En-tête : Référence et navigation --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('incidents.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-brand-blue hover:border-brand-blue transition-colors shadow-sm" title="Retour aux incidents">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-sm font-bold border border-slate-200 shadow-sm">{{ $incident->reference }}</span>
                    @include('partials.badge', ['type' => 'priorite', 'value' => $incident->priorite])
                    @include('partials.badge', ['type' => 'statut', 'value' => $incident->statut])
                </div>
                <p class="text-sm text-slate-500 mt-2 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Signalé le {{ $incident->created_at->format('d/m/Y à H:i') }} par <strong class="text-slate-700">{{ $incident->declarant->name }}</strong>
                </p>
            </div>
        </div>
    </div>

    {{-- Grille principale --}}
    <div class="grid gap-6 lg:grid-cols-3">
        
        {{-- Colonne de gauche : Contexte de l'incident --}}
        <div class="space-y-6 lg:col-span-2">
            
            {{-- Carte principale --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 sm:p-8">
                    {{-- Détails --}}
                    <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Détails du signalement
                    </h2>

                    <dl class="grid gap-6 sm:grid-cols-2 text-sm mb-6">
                        {{-- Bureau --}}
                        <div>
                            <dt class="text-slate-500 mb-1">Bureau concerné</dt>
                            <dd>
                                @if ($u->isAdmin() || $u->role === 'agent')
                                    <a class="inline-flex items-center gap-2 font-medium text-brand-blue hover:text-brand-dark transition-colors bg-blue-50/50 px-2 py-1 rounded-md border border-blue-100" href="{{ route('bureaux.show', $incident->bureau) }}">
                                        <span class="font-mono text-xs bg-white px-1.5 py-0.5 rounded shadow-sm border border-blue-200">{{ $incident->bureau->numero }}</span>
                                        {{ $incident->bureau->libelle }} 
                                        <span class="text-slate-400 text-xs">({{ $incident->bureau->etage->nom ?? 'N/A' }})</span>
                                    </a>
                                @else
                                    <span class="font-medium text-slate-800">{{ $incident->bureau->numero }} — {{ $incident->bureau->libelle }}</span>
                                @endif
                            </dd>
                        </div>

                        {{-- Type --}}
                        <div>
                            <dt class="text-slate-500 mb-1">Type de problème</dt>
                            <dd class="font-medium text-slate-800 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                {{ $incident->type->nom }}
                            </dd>
                        </div>

                        {{-- Bien --}}
                        <div>
                            <dt class="text-slate-500 mb-1">Bien concerné</dt>
                            <dd class="font-medium text-slate-800">
                                @if($incident->bien)
                                    {{ $incident->bien->designation }}
                                @else
                                    <span class="text-slate-400 italic">Aucun bien précis</span>
                                @endif
                            </dd>
                        </div>

                        {{-- Assigné à --}}
                        <div>
                            <dt class="text-slate-500 mb-1">Assigné à</dt>
                            <dd class="font-medium text-slate-800">
                                @if ($incident->assigne)
                                    <div class="flex items-center gap-2 mt-0.5">
                                        <div class="w-6 h-6 rounded-full bg-brand-blue/10 flex items-center justify-center text-[10px] font-bold text-brand-blue">
                                            {{ substr($incident->assigne->name, 0, 1) }}
                                        </div>
                                        {{ $incident->assigne->name }}
                                    </div>
                                @else
                                    <span class="text-slate-400 italic">Non assigné</span>
                                @endif
                            </dd>
                        </div>

                        {{-- Résolu le --}}
                        @if ($incident->resolu_le)
                            <div class="sm:col-span-2 p-3 bg-green-50 border border-green-100 rounded-lg">
                                <dt class="text-green-700 text-xs font-semibold mb-0.5 uppercase tracking-wide">Résolu le</dt>
                                <dd class="text-green-800 font-medium">{{ $incident->resolu_le->format('d/m/Y à H:i') }}</dd>
                            </div>
                        @endif
                    </dl>

                    {{-- Description --}}
                    <div class="pt-4 border-t border-slate-100">
                        <h3 class="text-sm font-semibold text-slate-700 mb-2">Description du problème</h3>
                        <p class="whitespace-pre-line text-slate-700 text-sm bg-slate-50 p-4 rounded-xl border border-slate-100 leading-relaxed">{{ $incident->description }}</p>
                    </div>
                </div>

                {{-- Photo --}}
                @if ($incident->photo)
                    <div class="border-t border-slate-100 bg-slate-50/50 p-4">
                        <h3 class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3 px-2">Preuve visuelle</h3>
                        <a href="{{ asset('storage/'.$incident->photo) }}" target="_blank" class="block group relative rounded-xl overflow-hidden border border-slate-200">
                            <img src="{{ asset('storage/'.$incident->photo) }}" class="w-full max-h-96 object-cover group-hover:scale-105 transition-transform duration-500" alt="Photo de l'incident">
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <span class="opacity-0 group-hover:opacity-100 bg-white/90 text-slate-800 px-3 py-1.5 rounded-full text-xs font-semibold shadow-sm flex items-center gap-1.5 transform translate-y-2 group-hover:translate-y-0 transition-all">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                    Agrandir
                                </span>
                            </div>
                        </a>
                    </div>
                @endif
            </div>

            {{-- Timeline des commentaires --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Suivi et commentaires
                </h2>
                
                <div class="relative border-l border-slate-200 ml-3 space-y-6">
                    @forelse ($incident->commentaires as $c)
                        <div class="relative pl-6">
                            {{-- Point de la timeline --}}
                            <span class="absolute -left-[5px] top-1.5 h-2.5 w-2.5 rounded-full bg-brand-blue ring-4 ring-white"></span>
                            
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 mb-1.5">
                                <div class="font-semibold text-slate-800 text-sm flex items-center gap-2">
                                    <div class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-600">
                                        {{ substr($c->user->name ?? 'S', 0, 1) }}
                                    </div>
                                    {{ $c->user->name ?? 'Système' }}
                                </div>
                                <div class="text-xs text-slate-400">{{ $c->created_at->format('d/m/Y à H:i') }}</div>
                            </div>
                            
                            @if ($c->nouveau_statut)
                                <div class="inline-flex items-center gap-1.5 text-xs bg-slate-50 border border-slate-200 px-2 py-1 rounded-md mb-2 text-slate-600">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                    Statut : <span class="line-through text-slate-400">{{ config('intendance.statuts_incident.'.$c->ancien_statut) }}</span> 
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    <span class="font-semibold text-slate-700">{{ config('intendance.statuts_incident.'.$c->nouveau_statut) }}</span>
                                </div>
                            @endif
                            
                            @if ($c->contenu)
                                <div class="text-sm text-slate-700 bg-slate-50 p-3 rounded-xl border border-slate-100">{{ $c->contenu }}</div>
                            @endif
                        </div>
                    @empty
                        <div class="pl-6 py-2 text-sm text-slate-500 italic">Aucun suivi pour le moment.</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Colonne de droite : Actions --}}
        <div class="space-y-6">
            
            {{-- Formulaire de traitement --}}
            @if ($u->isAdmin() || $u->isMaintenance())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-brand-dark text-white">
                    <h2 class="font-heading font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Traiter l'incident
                    </h2>
                </div>
                <form method="POST" action="{{ route('incidents.update', $incident) }}" class="p-6 space-y-4 text-sm">
                    @csrf @method('PUT')
                    
                    {{-- Statut --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5" for="statut">Mettre à jour le statut</label>
                        <select id="statut" name="statut" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
                            @foreach (config('intendance.statuts_incident') as $k => $l)
                                @if ($u->isAdmin() || in_array($k, ['en_cours', 'resolu', $incident->statut]))
                                    <option value="{{ $k }}" @selected($incident->statut === $k)>{{ $l }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>

                    {{-- Assignation (Admin uniquement) --}}
                    @if ($u->isAdmin())
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5" for="assigne_a">Assigner à un technicien</label>
                        <select id="assigne_a" name="assigne_a" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
                            <option value="">— Personne —</option>
                            @foreach ($techniciens as $t)
                                <option value="{{ $t->id }}" @selected($incident->assigne_a === $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Commentaire --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1.5" for="commentaire">Ajouter un commentaire</label>
                        <textarea id="commentaire" name="commentaire" rows="3" placeholder="Expliquez les actions menées..." class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white placeholder-slate-400"></textarea>
                    </div>

                    {{-- Bouton --}}
                    <button type="submit" class="w-full flex justify-center items-center gap-2 rounded-xl bg-brand-dark px-4 py-2.5 font-semibold text-white hover:bg-slate-800 transition-colors shadow-sm">
                        <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Enregistrer les modifications
                    </button>
                </form>
            </div>
            @endif

            {{-- Journal des notifications WhatsApp (Admin) --}}
            @if ($u->isAdmin())
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-green-50 rounded-lg text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <h2 class="font-heading font-semibold text-slate-800">Notifications WhatsApp</h2>
                </div>
                
                <div class="space-y-2">
                    @forelse ($incident->notifications as $n)
                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-100 text-sm">
                            <span class="text-slate-600 font-mono text-xs">{{ $n->destinataire }}</span>
                            @include('partials.badge', ['type' => 'notif', 'value' => $n->statut])
                        </div>
                    @empty
                        <div class="text-center py-4 bg-slate-50 rounded-xl border border-dashed border-slate-200">
                            <p class="text-xs text-slate-500">Aucune notification envoyée.</p>
                            <p class="text-[10px] text-slate-400 mt-1">Vérifiez la configuration WhatsApp.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection