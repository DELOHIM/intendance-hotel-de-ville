@extends('layouts.guest')

@section('title', 'Connexion')

@section('content')
<div class="min-h-screen flex w-full bg-slate-50 font-sans" 
     x-data="{ showIncidentForm: {{ $errors->has('bureau_id') || $errors->has('type_incident_id') || $errors->has('declarant_nom') ? 'true' : 'false' }} }">
    
    {{-- Colonne de gauche --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-brand-dark overflow-hidden items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-dark via-slate-800 to-brand-dark opacity-90"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-blue opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-brand-yellow opacity-10 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col items-center text-center px-12">
            <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo Ville de Kinshasa" class="h-32 w-auto object-contain bg-white rounded-full p-2 shadow-2xl mb-8">
            <h1 class="font-heading text-4xl font-bold text-white tracking-wide mb-4">Hôtel de Ville<br>de Kinshasa</h1>
            <p class="text-slate-300 text-lg max-w-md">Plateforme officielle de gestion de l'intendance, des bureaux et des équipements.</p>
            <div class="mt-12 flex gap-1 h-1 w-24 rounded-full overflow-hidden">
                <div class="w-1/3 bg-brand-blue"></div>
                <div class="w-1/3 bg-brand-yellow"></div>
                <div class="w-1/3 bg-brand-red"></div>
            </div>
        </div>
    </div>

    {{-- Colonne de droite --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            
            {{-- Logo mobile --}}
            <div class="lg:hidden flex flex-col items-center mb-8 text-center">
                <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo" class="h-20 w-auto mb-4">
                <h1 class="font-heading text-2xl font-bold text-slate-800">Hôtel de Ville de Kinshasa</h1>
            </div>

            {{-- Message de succès --}}
            @if (session('success'))
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 shadow-sm flex items-start gap-3">
                    <div class="p-1.5 rounded-full bg-green-100 text-green-600 shrink-0 mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>{{ session('success') }}</div>
                </div>
            @endif

            {{-- ============================================ --}}
            {{-- SECTION 1 : FORMULAIRE DE CONNEXION --}}
            {{-- ============================================ --}}
            <div x-show="!showIncidentForm" x-transition.opacity.duration.300ms>
                <div class="mb-8">
                    <h2 class="font-heading text-3xl font-bold text-slate-800">Bienvenue</h2>
                    <p class="text-slate-500 mt-2">Veuillez vous connecter à votre compte pour continuer.</p>
                </div>

                <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Adresse email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                            </div>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                                   class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm" placeholder="admin@hotelville-kin.cd">
                        </div>
                        @error('email')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Mot de passe</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <input id="password" type="password" name="password" required
                                   class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm" placeholder="••••••••">
                        </div>
                        @error('password')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex items-center justify-between text-sm pt-1">
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="checkbox" name="remember" class="rounded border-slate-300 text-brand-blue focus:ring-brand-blue focus:ring-offset-0 h-4 w-4 transition-colors">
                            <span class="text-slate-600 group-hover:text-slate-800 transition-colors">Se souvenir de moi</span>
                        </label>
                        <a href="{{ route('password.request') }}" class="font-medium text-brand-blue hover:text-brand-dark transition-colors">Mot de passe oublié ?</a>
                    </div>

                    <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-brand-dark hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all duration-200 shadow-md hover:shadow-lg">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-brand-blue group-hover:text-brand-yellow transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                        </span>
                        Se connecter
                    </button>
                </form>

                {{-- Séparateur --}}
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-3 bg-slate-50 text-slate-400">OU</span>
                    </div>
                </div>

                <button type="button" @click="showIncidentForm = true" 
                        class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl bg-brand-red text-white font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Signaler un incident sans se connecter
                </button>
                <p class="text-center text-xs text-slate-500 mt-3">Vous n'avez pas besoin de compte pour signaler un problème.</p>
            </div>

            {{-- ============================================ --}}
            {{-- SECTION 2 : FORMULAIRE DE SIGNALEMENT --}}
            {{-- ============================================ --}}
            <div x-show="showIncidentForm" x-cloak x-transition.opacity.duration.300ms>
                <button type="button" @click="showIncidentForm = false" 
                        class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour à la connexion
                </button>

                <div class="mb-6">
                    <h2 class="font-heading text-2xl font-bold text-slate-800">Signaler un incident</h2>
                    <p class="text-slate-500 mt-1 text-sm">Remplissez ce formulaire pour signaler un problème. Les administrateurs seront notifiés par WhatsApp.</p>
                </div>

                <form method="POST" action="{{ route('public.incidents.store') }}" class="space-y-4">
                    @csrf
                    
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="declarant_nom">Votre nom <span class="text-brand-red">*</span></label>
                        <input id="declarant_nom" name="declarant_nom" value="{{ old('declarant_nom') }}" required
                               class="block w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white" 
                               placeholder="Ex: Jean Kabila">
                        @error('declarant_nom')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="declarant_telephone">Votre téléphone (optionnel)</label>
                        <input id="declarant_telephone" name="declarant_telephone" value="{{ old('declarant_telephone') }}"
                               class="block w-full px-4 py-2.5 border border-slate-300 rounded-xl text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white" 
                               placeholder="+243...">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="bureau_id">Bureau concerné <span class="text-brand-red">*</span></label>
                        <select id="bureau_id" name="bureau_id" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white">
                            <option value="">— Choisir le bureau —</option>
                            @foreach ($bureaux as $b)
                                <option value="{{ $b->id }}" @selected(old('bureau_id') == $b->id)>
                                    {{ $b->numero }} — {{ $b->libelle }} ({{ $b->etage->nom ?? '' }})
                                </option>
                            @endforeach
                        </select>
                        @error('bureau_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="type_incident_id">Type de problème <span class="text-brand-red">*</span></label>
                        <select id="type_incident_id" name="type_incident_id" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white">
                            <option value="">— Choisir un type —</option>
                            @foreach ($types as $t)
                                <option value="{{ $t->id }}" @selected(old('type_incident_id') == $t->id)>{{ $t->nom }}</option>
                            @endforeach
                        </select>
                        @error('type_incident_id')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="priorite">Niveau d'urgence</label>
                        <select id="priorite" name="priorite" required
                                class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white">
                            <option value="faible" @selected(old('priorite') === 'faible')>Faible</option>
                            <option value="moyenne" @selected(old('priorite', 'moyenne') === 'moyenne')>Moyenne</option>
                            <option value="haute" @selected(old('priorite') === 'haute')>Haute</option>
                            <option value="critique" @selected(old('priorite') === 'critique')>Critique</option>
                        </select>
                        @error('priorite')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="description">Description détaillée <span class="text-brand-red">*</span></label>
                        <textarea id="description" name="description" rows="4" required minlength="10"
                                  class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white placeholder-slate-400"
                                  placeholder="Décrivez précisément le problème constaté...">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                    </div>

                    <button type="submit" class="w-full flex justify-center items-center gap-2 py-3 px-4 rounded-xl bg-brand-red text-white font-semibold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-red transition-all shadow-md hover:shadow-lg">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                        Envoyer le signalement
                    </button>
                </form>
            </div>

            <p class="mt-10 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Hôtel de Ville de Kinshasa. Tous droits réservés.
            </p>
        </div>
    </div>
</div>
@endsection