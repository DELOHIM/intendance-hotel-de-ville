@extends('layouts.app')

@section('title', 'Utilisateurs')

@section('content')
<div class="max-w-7xl mx-auto">
    
    {{-- En-tête de page --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">Gestion des utilisateurs</h1>
            <p class="text-slate-500 mt-1 text-sm">Gérez les comptes, les rôles et les accès à la plateforme.</p>
        </div>
        <a href="{{ route('users.create') }}" 
           class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
            <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvel utilisateur
        </a>
    </div>

    {{-- Barre de filtres --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6">
        <form class="grid grid-cols-1 sm:grid-cols-3 gap-3 items-end text-sm">
            {{-- Recherche --}}
            <div>
                <label for="q" class="block text-xs font-semibold text-slate-500 mb-1">Recherche</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <input id="q" name="q" value="{{ request('q') }}" placeholder="Nom ou email..." 
                           class="w-full rounded-xl border border-slate-300 pl-9 pr-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors">
                </div>
            </div>

            {{-- Rôle --}}
            <div>
                <label for="role" class="block text-xs font-semibold text-slate-500 mb-1">Rôle</label>
                <select id="role" name="role" class="w-full rounded-xl border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue focus:outline-none transition-colors bg-white">
                    <option value="">Tous les rôles</option>
                    @foreach (config('intendance.roles') as $k => $l)
                        <option value="{{ $k }}" @selected(request('role') == $k)>{{ $l }}</option>
                    @endforeach
                </select>
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

    {{-- Tableau des utilisateurs --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Utilisateur</th>
                        <th class="px-6 py-4">Contact</th>
                        <th class="px-6 py-4">Rôle</th>
                        <th class="px-6 py-4">Statut</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $u)
                        <tr class="hover:bg-slate-50/70 transition-colors group">
                            
                            {{-- Utilisateur (Avatar + Nom) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-brand-blue to-brand-blue/70 flex items-center justify-center text-white font-bold text-sm shadow-sm shrink-0">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-slate-800 group-hover:text-brand-blue transition-colors">
                                            {{ $u->name }}
                                            @if ($u->id === auth()->id())
                                                <span class="ml-1 text-[10px] bg-brand-yellow/20 text-amber-700 px-1.5 py-0.5 rounded font-semibold">Vous</span>
                                            @endif
                                        </div>
                                        <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Téléphone --}}
                            <td class="px-6 py-4">
                                @if ($u->phone)
                                    <span class="font-mono text-xs text-slate-600 bg-slate-100 px-2 py-1 rounded-md border border-slate-200">
                                        {{ $u->phone }}
                                    </span>
                                @else
                                    <span class="text-slate-300 italic text-xs">Non renseigné</span>
                                @endif
                            </td>

                            {{-- Rôle --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-semibold 
                                    @if($u->role === 'super_admin') bg-purple-50 text-purple-700 border border-purple-200
                                    @elseif($u->role === 'admin') bg-blue-50 text-blue-700 border border-blue-200
                                    @elseif($u->role === 'maintenance') bg-amber-50 text-amber-700 border border-amber-200
                                    @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                    {{ $u->roleLabel() }}
                                </span>
                            </td>

                            {{-- Statut --}}
                            <td class="px-6 py-4">
                                @if ($u->active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700 border border-green-200 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-red-50 text-brand-red border border-red-200 shadow-sm">
                                        <span class="w-1.5 h-1.5 rounded-full bg-brand-red"></span>
                                        Désactivé
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1">
                                    <a href="{{ route('users.edit', $u) }}" 
                                       class="p-1.5 rounded-md text-slate-400 hover:text-brand-blue hover:bg-blue-50 transition-colors" title="Modifier">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    </a>
                                    
                                    @if ($u->id !== auth()->id())
                                        <form method="POST" action="{{ route('users.toggle', $u) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir {{ $u->active ? 'désactiver' : 'activer' }} ce compte ?')">
                                            @csrf
                                            <button type="submit" class="p-1.5 rounded-md transition-colors 
                                                {{ $u->active 
                                                    ? 'text-slate-400 hover:text-brand-red hover:bg-red-50' 
                                                    : 'text-slate-400 hover:text-green-600 hover:bg-green-50' }}" 
                                                title="{{ $u->active ? 'Désactiver' : 'Activer' }}">
                                                @if($u->active)
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                @else
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                <p class="font-medium text-slate-600">Aucun utilisateur trouvé</p>
                                <p class="text-sm mt-1">Essayez de modifier vos filtres ou créez un nouvel utilisateur.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection