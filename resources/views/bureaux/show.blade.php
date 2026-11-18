@extends('layouts.app')

@section('title', 'Bureau '.$bureau->numero)

@section('content')
@php($admin = auth()->user()->isAdmin())

<div class="max-w-6xl mx-auto">
    
    {{-- En-tête : Titre et Bouton Retour --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('bureaux.index') }}" class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white border border-slate-200 text-slate-500 hover:text-brand-blue hover:border-brand-blue transition-colors shadow-sm" title="Retour à la liste des bureaux">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            </a>
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 font-mono text-sm font-bold border border-slate-200 shadow-sm">{{ $bureau->numero }}</span>
                    <h1 class="font-heading text-2xl font-bold text-slate-800">{{ $bureau->libelle }}</h1>
                </div>
                <p class="text-sm text-slate-500 mt-1 flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    {{ $bureau->etage->nom }}
                    @if ($bureau->service)
                        <span class="text-slate-300 mx-1">|</span>
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        {{ $bureau->service }}
                    @endif
                </p>
            </div>
        </div>

        @if ($admin)
        <div class="flex items-center gap-2">
            <a href="{{ route('bureaux.edit', $bureau) }}" class="inline-flex items-center gap-2 rounded-xl border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Modifier
            </a>
            <form method="POST" action="{{ route('bureaux.destroy', $bureau) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir archiver ce bureau ? Vous pourrez le restaurer plus tard.')">
                @csrf @method('DELETE')
                <button class="inline-flex items-center gap-2 rounded-xl border border-amber-200 bg-amber-50 px-4 py-2 text-sm font-semibold text-amber-700 hover:bg-amber-100 transition-colors shadow-sm" title="Archiver le bureau">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                    Archiver
                </button>
            </form>
        </div>
        @endif
    </div>

    {{-- Grille principale --}}
    <div class="grid gap-6 lg:grid-cols-3">
        
        {{-- Colonne de gauche (Détails + Agents) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Carte d'informations --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Informations
                </h2>

                @if ($bureau->description)
                    <p class="text-slate-700 text-sm bg-slate-50 p-4 rounded-xl border border-slate-100 italic mb-6">
                        {{ $bureau->description }}
                    </p>
                @endif

                <div class="flex items-start gap-4">
                    <div class="p-3 bg-brand-blue/10 rounded-xl text-brand-blue shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-slate-700 mb-1">Agents responsables</h3>
                        <p class="text-slate-600 text-sm">
                            {{ $bureau->agents->pluck('name')->join(', ') ?: 'Aucun agent affecté à ce bureau.' }}
                        </p>
                    </div>
                </div>

                {{-- Bouton Signaler Incident --}}
                <div class="mt-6 pt-6 border-t border-slate-100">
                    <a href="{{ route('incidents.create', ['bureau_id' => $bureau->id]) }}" 
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-red px-5 py-3 text-sm font-semibold text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        Signaler un incident dans ce bureau
                    </a>
                </div>
            </div>

            {{-- Tableau des biens --}}
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="font-heading font-semibold text-slate-800 flex items-center gap-2">
                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        Biens du bureau
                        <span class="bg-slate-200 text-slate-600 text-xs px-2 py-0.5 rounded-full ml-2">{{ $biens->count() }}</span>
                    </h2>
                    @if ($admin)
                        <a href="{{ route('biens.create', ['bureau_id' => $bureau->id]) }}" class="text-sm font-medium text-brand-blue hover:text-brand-dark inline-flex items-center gap-1 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Ajouter
                        </a>
                    @endif
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Désignation</th>
                                <th class="px-6 py-3">Catégorie</th>
                                <th class="px-6 py-3">N° série</th>
                                <th class="px-6 py-3">État</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($biens as $b)
                                <tr class="hover:bg-slate-50/70 transition-colors group">
                                    <td class="px-6 py-3 font-medium text-slate-800">
                                        @if ($admin)
                                            <a class="group-hover:text-brand-blue transition-colors" href="{{ route('biens.show', $b) }}">{{ $b->designation }}</a>
                                        @else
                                            {{ $b->designation }}
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 text-slate-600">{{ $b->categorie->nom }}</td>
                                    <td class="px-6 py-3 text-slate-500 font-mono text-xs">{{ $b->numero_serie ?: '—' }}</td>
                                    <td class="px-6 py-3">@include('partials.badge', ['type' => 'etat', 'value' => $b->etat])</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-slate-500">
                                        <svg class="mx-auto h-8 w-8 text-slate-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                                        Aucun bien enregistré dans ce bureau.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Colonne de droite (QR Code) --}}
        <div class="space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 text-center">
                <div class="mx-auto w-48 p-3 bg-white border border-slate-200 rounded-2xl shadow-sm mb-4">
                    {!! $qrSvg !!}
                </div>
                <h3 class="font-heading font-semibold text-slate-800 mb-1">QR Code du bureau</h3>
                <p class="text-xs text-slate-400 break-all mb-4">{{ $bureau->scanUrl() }}</p>
                
                @if ($admin)
                <div class="flex flex-wrap justify-center gap-2 text-sm border-t border-slate-100 pt-4">
                    <a href="{{ route('bureaux.qr', ['bureau' => $bureau, 'format' => 'svg']) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-blue transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        SVG
                    </a>
                    @if (extension_loaded('imagick'))
                    <a href="{{ route('bureaux.qr', ['bureau' => $bureau, 'format' => 'png']) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-blue transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        PNG
                    </a>
                    @endif
                    <a href="{{ route('bureaux.qr.pdf', $bureau) }}" class="inline-flex items-center gap-1 rounded-lg border border-slate-300 bg-white px-3 py-1.5 font-medium text-slate-600 hover:bg-slate-50 hover:text-brand-blue transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        PDF
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Historique des incidents --}}
    <div class="mt-6 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8">
        <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-6 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Historique des incidents
        </h2>
        
        <div class="relative border-l border-slate-200 ml-3 space-y-6">
            @forelse ($incidents as $i)
                <a href="{{ route('incidents.show', $i) }}" class="block relative pl-6 group">
                    {{-- Point de la timeline --}}
                    <span class="absolute -left-[5px] top-1.5 h-2.5 w-2.5 rounded-full bg-brand-red ring-4 ring-white group-hover:scale-125 transition-transform"></span>
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 p-3 rounded-xl border border-transparent group-hover:border-slate-200 group-hover:bg-slate-50 transition-all">
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="font-mono text-xs font-bold text-slate-500 bg-white px-2 py-0.5 rounded border border-slate-200">{{ $i->reference }}</span>
                                <span class="text-sm font-semibold text-slate-800">{{ $i->type->nom }}</span>
                            </div>
                            <div class="text-xs text-slate-500 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Signalé le {{ $i->created_at->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @include('partials.badge', ['type' => 'priorite', 'value' => $i->priorite]) 
                            @include('partials.badge', ['type' => 'statut', 'value' => $i->statut])
                        </div>
                    </div>
                </a>
            @empty
                <div class="pl-6 text-sm text-slate-500 py-2">Aucun incident signalé dans ce bureau.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection