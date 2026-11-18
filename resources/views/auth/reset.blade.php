@extends('layouts.guest')

@section('title', 'Nouveau mot de passe')

@section('content')
<div class="min-h-screen flex w-full bg-slate-50 font-sans">
    
    {{-- Colonne de gauche : Identité visuelle --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-brand-dark overflow-hidden items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-dark via-slate-800 to-brand-dark opacity-90"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-blue opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-brand-yellow opacity-10 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col items-center text-center px-12">
            <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo Ville de Kinshasa" class="h-32 w-auto object-contain bg-white rounded-full p-2 shadow-2xl mb-8">
            <h1 class="font-heading text-4xl font-bold text-white tracking-wide mb-4">Hôtel de Ville<br>de Kinshasa</h1>
            <p class="text-slate-300 text-lg max-w-md">Sécurisez votre compte en définissant un nouveau mot de passe.</p>
            <div class="mt-12 flex gap-1 h-1 w-24 rounded-full overflow-hidden">
                <div class="w-1/3 bg-brand-blue"></div>
                <div class="w-1/3 bg-brand-yellow"></div>
                <div class="w-1/3 bg-brand-red"></div>
            </div>
        </div>
    </div>

    {{-- Colonne de droite : Formulaire --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12">
        <div class="w-full max-w-md">
            
            {{-- Logo affiché uniquement sur mobile --}}
            <div class="lg:hidden flex flex-col items-center mb-8 text-center">
                <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo" class="h-20 w-auto mb-4">
                <h1 class="font-heading text-2xl font-bold text-slate-800">Hôtel de Ville de Kinshasa</h1>
            </div>

            <div class="mb-8">
                <h2 class="font-heading text-3xl font-bold text-slate-800">Nouveau mot de passe</h2>
                <p class="text-slate-500 mt-2">Créez un mot de passe solide pour protéger votre accès au système.</p>
            </div>

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                {{-- Champ Email --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="email">Adresse email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/></svg>
                        </div>
                        <input id="email" type="email" name="email" value="{{ old('email', $email) }}" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 bg-slate-50 focus:bg-white focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm shadow-sm">
                    </div>
                    @error('email')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Champ Nouveau mot de passe --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password">Nouveau mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <input id="password" type="password" name="password" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm" placeholder="••••••••">
                    </div>
                    <p class="mt-1.5 text-xs text-slate-500 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        8 caractères minimum, avec lettres et chiffres.
                    </p>
                    @error('password')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Champ Confirmation --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="password_confirmation">Confirmation du mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                               class="block w-full pl-10 pr-3 py-3 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors sm:text-sm bg-white shadow-sm" placeholder="••••••••">
                    </div>
                </div>

                {{-- Bouton de validation --}}
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-brand-dark hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all duration-200 shadow-md hover:shadow-lg mt-2">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-brand-blue group-hover:text-brand-yellow transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </span>
                    Réinitialiser le mot de passe
                </button>
            </form>

            {{-- Pied de page --}}
            <p class="mt-12 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Hôtel de Ville de Kinshasa. Tous droits réservés.
            </p>
        </div>
    </div>
</div>
@endsection