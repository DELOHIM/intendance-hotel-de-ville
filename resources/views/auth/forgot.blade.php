@extends('layouts.guest')

@section('title', 'Mot de passe oublié')

@section('content')
<div class="min-h-screen flex w-full bg-slate-50 font-sans">
    
    {{-- Colonne de gauche : Identité visuelle (identique à la page de connexion) --}}
    <div class="hidden lg:flex lg:w-1/2 relative bg-brand-dark overflow-hidden items-center justify-center">
        <div class="absolute inset-0 bg-gradient-to-br from-brand-dark via-slate-800 to-brand-dark opacity-90"></div>
        <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-brand-blue opacity-20 blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-brand-yellow opacity-10 blur-3xl"></div>
        
        <div class="relative z-10 flex flex-col items-center text-center px-12">
            <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo Ville de Kinshasa" class="h-32 w-auto object-contain bg-white rounded-full p-2 shadow-2xl mb-8">
            <h1 class="font-heading text-4xl font-bold text-white tracking-wide mb-4">Hôtel de Ville<br>de Kinshasa</h1>
            <p class="text-slate-300 text-lg max-w-md">Récupérez l'accès à votre compte pour gérer l'intendance.</p>
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
                <h2 class="font-heading text-3xl font-bold text-slate-800">Mot de passe oublié ?</h2>
                <p class="text-slate-500 mt-2">Pas de panique. Saisissez votre adresse email et nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
            </div>

            {{-- Messages de succès (ex: email envoyé) --}}
            @if (session('status'))
                <div class="mb-6 rounded-xl bg-green-50 p-4 border border-green-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-green-800">{{ session('status') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Formulaire --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf
                
                {{-- Champ Email --}}
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

                {{-- Bouton d'envoi --}}
                <button type="submit" class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-semibold rounded-xl text-white bg-brand-dark hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all duration-200 shadow-md hover:shadow-lg">
                    <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                        <svg class="h-5 w-5 text-brand-blue group-hover:text-brand-yellow transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </span>
                    Envoyer le lien de réinitialisation
                </button>
            </form>

            {{-- Retour à la connexion --}}
            <div class="mt-8 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-brand-blue transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Retour à la page de connexion
                </a>
            </div>

            {{-- Pied de page --}}
            <p class="mt-12 text-center text-xs text-slate-400">
                &copy; {{ date('Y') }} Hôtel de Ville de Kinshasa. Tous droits réservés.
            </p>
        </div>
    </div>
</div>
@endsection