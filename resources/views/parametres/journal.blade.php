@extends('layouts.app')

@section('title', 'Journal des envois WhatsApp')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-6">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Journal des envois WhatsApp</h1>
        <p class="text-slate-500 mt-1 text-sm">Suivi technique des notifications envoyées aux administrateurs via Green API.</p>
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="flex flex-col sm:flex-row gap-3 items-end text-sm">
            <div class="w-full sm:w-64">
                <label for="statut" class="block text-xs font-semibold text-slate-500 mb-1">Filtrer par statut</label>
                <select id="statut" name="statut" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les statuts</option>
                    @foreach (['envoye' => 'Envoyé', 'en_attente' => 'En attente', 'echec' => 'Échec'] as $k => $l)
                        <option value="{{ $k }}" @selected(request('statut') == $k)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="h-[38px] px-5 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-medium transition-colors border border-slate-300 flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                Filtrer
            </button>
        </form>
    </div>

    {{-- Tableau --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">Date</th>
                        <th class="px-6 py-4">Incident lié</th>
                        <th class="px-6 py-4">Destinataire</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-center">Tentatives</th>
                        <th class="px-6 py-4">Erreur éventuelle</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($logs as $l)
                        <tr class="hover:bg-slate-50/70 transition-colors align-top group">
                            
                            {{-- Date --}}
                            <td class="px-6 py-4 whitespace-nowrap text-xs">
                                <div class="flex flex-col">
                                    <span class="font-medium text-slate-700">{{ $l->created_at->format('d/m/Y') }}</span>
                                    <span class="text-slate-400 font-mono mt-0.5">{{ $l->created_at->format('H:i') }}</span>
                                </div>
                            </td>

                            {{-- Incident --}}
                            <td class="px-6 py-4">
                                @if ($l->incident)
                                    <a class="inline-flex items-center gap-1.5 font-mono text-xs font-bold text-brand-blue bg-blue-50 px-2 py-1 rounded-md border border-blue-100 hover:bg-blue-100 transition-colors" href="{{ route('incidents.show', $l->incident) }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        {{ $l->incident->reference }}
                                    </a>
                                @else
                                    <span class="text-slate-400 italic">—</span>
                                @endif
                            </td>
                            
                            {{-- Destinataire --}}
                            <td class="px-6 py-4">
                                <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">
                                    {{ $l->destinataire }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4">
                                @include('partials.badge', ['type' => 'notif', 'value' => $l->statut])
                            </td>

                            {{-- Tentatives --}}
                            <td class="px-6 py-4 text-center">
                                @if ($l->tentatives > 1)
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 text-xs font-bold border border-amber-100">
                                        {{ $l->tentatives }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center min-w-[24px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-xs font-bold border border-slate-200">
                                        {{ $l->tentatives }}
                                    </span>
                                @endif
                            </td>

                            {{-- Erreur --}}
                            <td class="px-6 py-4 max-w-xs">
                                @if ($l->erreur)
                                    <div class="text-xs text-brand-red bg-red-50 p-2 rounded-lg border border-red-100 break-words leading-relaxed" title="{{ $l->erreur }}">
                                        {{ \Illuminate\Support\Str::limit($l->erreur, 140) }}
                                    </div>
                                @else
                                    <span class="text-slate-300 italic">Aucune erreur</span>
                                @endif
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-4 text-right">
                                @if ($l->statut === 'echec')
                                    <form method="POST" action="{{ route('notifications.retry', $l) }}">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-brand-dark hover:text-white hover:border-brand-dark transition-all shadow-sm">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            Renvoyer
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-300 text-xs">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                <p class="font-medium text-slate-600">Aucun envoi enregistré</p>
                                <p class="text-sm mt-1">Les notifications WhatsApp apparaîtront ici dès qu'un incident sera signalé.</p>
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