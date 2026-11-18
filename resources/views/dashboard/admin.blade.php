@extends('layouts.app')

@section('title', 'Tableau de bord')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
@endpush

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    {{-- En-tête de bienvenue --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">Bonjour, {{ auth()->user()->name }} 👋</h1>
            <p class="text-slate-500 mt-1 text-sm">Voici un aperçu de l'activité de l'Hôtel de Ville aujourd'hui.</p>
        </div>
        <div class="text-sm text-slate-500 bg-white px-4 py-2 rounded-xl border border-slate-200 shadow-sm flex items-center gap-2">
            <svg class="w-4 h-4 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            {{ now()->translatedFormat('l d F Y') }}
        </div>
    </div>

    {{-- Cartes de statistiques (KPI) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @php
            $kpis = [
                ['Bureaux', $stats['bureaux'], 'brand-blue', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>'],
                ['Biens inventoriés', $stats['biens'], 'emerald', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>'],
                ['Incidents en cours', $stats['en_cours'], 'amber', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>'],
                ['Incidents critiques', $stats['critiques'], 'brand-red', '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>'],
            ];
        @endphp

        @foreach ($kpis as [$lib, $val, $color, $icon])
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 flex items-center justify-between relative overflow-hidden group hover:shadow-md transition-shadow">
                <div class="absolute top-0 right-0 -mr-4 -mt-4 w-20 h-20 rounded-full bg-{{ $color }}/5 group-hover:bg-{{ $color }}/10 transition-colors"></div>
                <div class="relative z-10">
                    <p class="text-sm font-medium text-slate-500 mb-1">{{ $lib }}</p>
                    <p class="text-3xl font-bold text-{{ $color }}">{{ $val }}</p>
                </div>
                <div class="p-3 rounded-xl bg-{{ $color }}/10 text-{{ $color }} relative z-10">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">{!! $icon !!}</svg>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Section Alertes & Priorités --}}
    <div class="grid gap-6 lg:grid-cols-3">
        {{-- Incidents ouverts par priorité --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                Incidents ouverts par priorité
            </h2>
            <div class="space-y-3">
                @foreach (config('intendance.priorites') as $cle => $lib)
                    <div class="flex items-center justify-between p-2 rounded-lg hover:bg-slate-50 transition-colors">
                        @include('partials.badge', ['type' => 'priorite', 'value' => $cle])
                        <span class="font-bold text-slate-700 bg-slate-100 px-2.5 py-0.5 rounded-md text-sm">{{ $parPriorite[$cle] ?? 0 }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Derniers incidents à traiter --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-semibold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-brand-red" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Nouveaux incidents à traiter
                </h2>
                <a href="{{ route('incidents.index', ['statut' => 'nouveau']) }}" class="text-xs font-semibold text-brand-blue bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition-colors">Tout voir</a>
            </div>
            
            <div class="space-y-2">
                @forelse ($derniers as $i)
                    <a href="{{ route('incidents.show', $i) }}" class="flex items-center justify-between p-3 rounded-xl border border-transparent hover:border-slate-200 hover:bg-slate-50 transition-all group">
                        <div class="flex items-center gap-3">
                            <span class="font-mono text-xs font-bold text-slate-500 bg-white border border-slate-200 px-2 py-1 rounded-md shadow-sm">{{ $i->reference }}</span>
                            <div>
                                <p class="text-sm font-semibold text-slate-800 group-hover:text-brand-blue transition-colors">{{ $i->type->nom ?? 'Incident' }}</p>
                                <p class="text-xs text-slate-500 flex items-center gap-1 mt-0.5">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Bureau {{ $i->bureau->numero ?? 'N/A' }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5">
                            @include('partials.badge', ['type' => 'priorite', 'value' => $i->priorite])
                            <span class="text-[10px] font-medium text-slate-400">{{ $i->created_at->diffForHumans() }}</span>
                        </div>
                    </a>
                @empty
                    <div class="text-center py-8">
                        <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-50 text-green-500 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <p class="text-sm font-medium text-slate-700">Aucun nouvel incident</p>
                        <p class="text-xs text-slate-500 mt-1">Tout est calme pour le moment. 🎉</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Section Filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-heading font-semibold text-slate-800">Analyse détaillée</h2>
            <p class="text-sm text-slate-500"><span class="font-bold text-brand-blue">{{ $totalFiltre }}</span> incident(s) dans la sélection</p>
        </div>

        <form method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6 items-end text-sm">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Du</label>
                <input type="date" name="du" value="{{ $filtres['du'] ?? '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Au</label>
                <input type="date" name="au" value="{{ $filtres['au'] ?? '' }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Étage</label>
                <select name="etage_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
                    <option value="">Tous</option>
                    @foreach ($etages as $e)<option value="{{ $e->id }}" @selected(($filtres['etage_id'] ?? '') == $e->id)>{{ $e->nom }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Type</label>
                <select name="type_incident_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
                    <option value="">Tous</option>
                    @foreach ($types as $t)<option value="{{ $t->id }}" @selected(($filtres['type_incident_id'] ?? '') == $t->id)>{{ $t->nom }}</option>@endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">Statut</label>
                <select name="statut" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue bg-white">
                    <option value="">Tous</option>
                    @foreach (config('intendance.statuts_incident') as $k => $l)<option value="{{ $k }}" @selected(($filtres['statut'] ?? '') == $k)>{{ $l }}</option>@endforeach
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="flex-1 rounded-xl bg-brand-dark text-white px-4 py-2 font-semibold hover:bg-slate-800 transition-colors shadow-sm">Filtrer</button>
                <a href="{{ route('dashboard') }}" class="flex items-center justify-center rounded-xl border border-slate-300 bg-white px-3 py-2 text-slate-500 hover:bg-slate-50 transition-colors" title="Réinitialiser">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                </a>
            </div>
        </form>
    </div>

    {{-- Section Graphiques --}}
    <div class="grid gap-6 md:grid-cols-2">
        @php
            $charts = [
                ['mois', 'Évolution mensuelle', 'line', ''],
                ['type', 'Répartition par type', 'doughnut', ''],
                ['etage', 'Incidents par étage', 'bar', ''],
                ['statut', 'Répartition par statut', 'pie', ''],
                ['bureau', 'Top 10 des bureaux les plus touchés', 'bar', 'md:col-span-2'],
            ];
        @endphp

        @foreach ($charts as [$id, $titre, $genre, $span])
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 {{ $span }}">
                <h3 class="font-heading font-semibold text-slate-800 mb-4">{{ $titre }}</h3>
                <div class="h-64 relative">
                    <canvas id="chart-{{ $id }}"></canvas>
                </div>
            </div>
        @endforeach
    </div>

</div>
@endsection

@push('scripts')
<script>
    // Configuration globale de Chart.js pour un style épuré
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#64748b'; // slate-500
    
    const graph = @json($graph);
    const palette = ['#007FFF', '#10b981', '#f59e0b', '#CE1021', '#8b5cf6', '#14b8a6', '#f97316', '#64748b', '#ec4899', '#6366f1'];
    const config = { mois: 'line', type: 'doughnut', etage: 'bar', statut: 'pie', bureau: 'bar' };

    Object.entries(config).forEach(([id, genre]) => {
        const d = graph[id];
        const multi = ['doughnut', 'pie'].includes(genre);
        
        new Chart(document.getElementById('chart-' + id), {
            type: genre,
            data: { 
                labels: d.labels, 
                datasets: [{
                    label: 'Incidents', 
                    data: d.data,
                    backgroundColor: multi ? palette : (genre === 'line' ? 'rgba(0, 127, 255, 0.1)' : '#007FFF'),
                    borderColor: genre === 'line' ? '#007FFF' : (multi ? '#fff' : '#007FFF'),
                    borderWidth: multi ? 2 : (genre === 'line' ? 2 : 0),
                    fill: genre === 'line', 
                    tension: 0.4,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: '#007FFF',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderRadius: genre === 'bar' ? 6 : 0,
                }] 
            },
            options: { 
                responsive: true, 
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: multi, 
                        position: 'bottom',
                        labels: { usePointStyle: true, boxWidth: 8, padding: 20 }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        padding: 12,
                        cornerRadius: 8,
                        titleFont: { size: 13, weight: 'bold' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: multi ? {} : { 
                    y: { 
                        beginAtZero: true, 
                        ticks: { precision: 0 },
                        grid: { borderDash: [4, 4], color: '#f1f5f9' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                } 
            },
        });
    });
</script>
@endpush