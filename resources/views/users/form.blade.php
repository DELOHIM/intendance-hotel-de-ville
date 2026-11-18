@extends('layouts.app')

@section('title', $user->exists ? 'Modifier l\'utilisateur' : 'Nouvel utilisateur')

@section('content')
<div class="max-w-3xl mx-auto">
    
    {{-- En-tête --}}
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="font-heading text-2xl font-bold text-slate-800">
                {{ $user->exists ? 'Modifier l\'utilisateur' : 'Créer un nouvel utilisateur' }}
            </h1>
            <p class="text-slate-500 mt-1 text-sm">
                {{ $user->exists ? 'Mettez à jour les informations et les droits d\'accès de cet utilisateur.' : 'Renseignez les informations pour créer un nouvel accès à la plateforme.' }}
            </p>
        </div>
        <a href="{{ route('users.index') }}" class="hidden sm:inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Retour
        </a>
    </div>

    {{-- Carte du formulaire --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form method="POST" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}" class="p-6 sm:p-8 space-y-6">
            @csrf 
            @if ($user->exists) @method('PUT') @endif

            {{-- Section 1 : Informations personnelles --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    Informations personnelles
                </h3>
                
                <div class="grid gap-5 sm:grid-cols-2">
                    {{-- Nom complet --}}
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="name">Nom complet <span class="text-brand-red">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <input id="name" name="name" value="{{ old('name', $user->name) }}" required 
                                   placeholder="Ex: Jean Kabila"
                                   class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        </div>
                        @error('name')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Adresse email <span class="text-brand-red">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required 
                                   placeholder="exemple@hotelville-kin.cd"
                                   class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        </div>
                        @error('email')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Téléphone --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="phone">Téléphone (WhatsApp)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            </div>
                            <input id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   placeholder="+243..."
                                   class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        </div>
                        @error('phone')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 2 : Rôle et sécurité --}}
            <div>
                <h3 class="text-sm font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    Rôle et sécurité
                </h3>
                
                <div class="space-y-5">
                    {{-- Rôle --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="role">Rôle de l'utilisateur <span class="text-brand-red">*</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            </div>
                            <select id="role" name="role" class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                                @foreach (config('intendance.roles') as $k => $l)
                                    <option value="{{ $k }}" @selected(old('role', $user->role) === $k)>{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('role')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    {{-- Mot de passe --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">
                            Mot de passe 
                            @if($user->exists)
                                <span class="text-slate-400 font-normal text-xs ml-1">(laisser vide pour conserver l'actuel)</span>
                            @endif
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="password" type="password" name="password" {{ $user->exists ? '' : 'required' }} 
                                   placeholder="••••••••"
                                   class="w-full rounded-xl border border-slate-300 pl-10 pr-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm">
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            8 caractères minimum, avec lettres et chiffres.
                        </p>
                        @error('password')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>
            </div>

            <hr class="border-slate-100">

            {{-- Section 3 : Statut du compte --}}
            <div>
                <label class="flex items-center gap-3 p-4 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition-colors">
                    <input type="checkbox" name="active" value="1" @checked(old('active', $user->active)) 
                           class="w-5 h-5 rounded border-slate-300 text-brand-blue focus:ring-brand-blue focus:ring-offset-0 transition-colors">
                    <div>
                        <p class="text-sm font-semibold text-slate-800">Compte actif</p>
                        <p class="text-xs text-slate-500">L'utilisateur pourra se connecter et accéder à la plateforme.</p>
                    </div>
                </label>
            </div>

            {{-- Actions --}}
            <div class="pt-6 flex flex-col-reverse sm:flex-row items-center justify-end gap-3 border-t border-slate-100">
                <a href="{{ route('users.index') }}" 
                   class="w-full sm:w-auto text-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" 
                        class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-brand-dark px-5 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                    <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    {{ $user->exists ? 'Mettre à jour l\'utilisateur' : 'Créer l\'utilisateur' }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection