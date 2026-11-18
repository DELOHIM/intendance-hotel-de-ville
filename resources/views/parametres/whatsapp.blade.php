@extends('layouts.app')

@section('title', 'Configuration WhatsApp')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    {{-- En-tête --}}
    <div class="mb-2">
        <h1 class="font-heading text-2xl font-bold text-slate-800">Configuration WhatsApp</h1>
        <p class="text-slate-500 mt-1 text-sm">Paramétrez les destinataires et le contenu des alertes envoyées via Green API.</p>
    </div>

    {{-- Bandeau de statut de connexion --}}
    <div class="rounded-2xl p-5 text-sm flex items-start gap-3 {{ $configure ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-amber-50 border border-amber-200 text-amber-800' }}">
        @if ($configure)
            <div class="p-1.5 rounded-full bg-green-100 text-green-600 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
            <div>
                <h3 class="font-bold">Connexion Green API opérationnelle</h3>
                <p class="mt-0.5 text-green-700">Les identifiants <code class="bg-white/50 px-1 rounded text-xs">GREEN_API_ID_INSTANCE</code> et <code class="bg-white/50 px-1 rounded text-xs">GREEN_API_TOKEN</code> sont bien présents dans le fichier <code>.env</code>.</p>
            </div>
        @else
            <div class="p-1.5 rounded-full bg-amber-100 text-amber-600 shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            </div>
            <div>
                <h3 class="font-bold">Configuration incomplète</h3>
                <p class="mt-1 text-amber-700 leading-relaxed">Identifiants Green API manquants. Veuillez renseigner <code class="bg-white/50 px-1 rounded text-xs">GREEN_API_ID_INSTANCE</code> et <code class="bg-white/50 px-1 rounded text-xs">GREEN_API_TOKEN</code> dans le fichier <code>.env</code>, puis relancer la commande :</p>
                <code class="inline-block mt-2 bg-amber-900/10 px-2 py-1 rounded text-xs font-mono">php artisan config:clear</code>
            </div>
        @endif
    </div>

    <div class="grid gap-6">
        {{-- Formulaire principal --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50/50 flex items-center gap-2">
                <div class="p-2 bg-brand-blue/10 rounded-lg text-brand-blue">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <h2 class="font-heading font-semibold text-slate-800">Paramètres d'envoi</h2>
            </div>

            <form method="POST" action="{{ route('whatsapp.update') }}" class="p-6 sm:p-8 space-y-6">
                @csrf @method('PUT')
                
                {{-- Numéros --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="numeros">Numéros WhatsApp des administrateurs <span class="text-brand-red">*</span></label>
                    <textarea id="numeros" name="numeros" rows="3" placeholder="+243812345678&#10;0990000000" 
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors bg-white shadow-sm">{{ old('numeros', $numeros) }}</textarea>
                    <p class="mt-2 text-xs text-slate-500 flex items-start gap-1.5">
                        <svg class="w-4 h-4 text-slate-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Un numéro par ligne (ou séparés par des virgules). Format international conseillé ; un numéro commençant par 0 reçoit automatiquement l'indicatif +243.
                    </p>
                    @error('numeros')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Template --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1.5" for="template">Modèle du message (Template) <span class="text-brand-red">*</span></label>
                    <textarea id="template" name="template" rows="10" required 
                              class="w-full rounded-xl border border-slate-300 px-4 py-3 font-mono text-sm focus:outline-none focus:ring-2 focus:ring-brand-blue/50 focus:border-brand-blue transition-colors bg-white shadow-sm">{{ old('template', $template) }}</textarea>
                    @error('template')<p class="mt-1.5 text-sm text-brand-red font-medium">{{ $message }}</p>@enderror
                </div>

                {{-- Variables disponibles --}}
                <div class="bg-slate-50 p-4 rounded-xl border border-slate-200">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20l4-16m2 16l4-16M6 9h14M4 15h14"/></svg>
                        Variables disponibles à insérer dans le message
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @php
                            $vars = ['reference', 'bureau', 'etage', 'type', 'priorite', 'description', 'agent', 'date'];
                        @endphp
                        @foreach ($vars as $v)
                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-white border border-slate-300 text-xs font-mono text-slate-700 shadow-sm cursor-pointer hover:border-brand-blue hover:text-brand-blue transition-colors" onclick="navigator.clipboard.writeText('{{'{'.$v.'}'}}')" title="Cliquer pour copier">
                                {{ '{'.$v.'}' }}
                            </span>
                        @endforeach
                    </div>
                </div>

                {{-- Bouton --}}
                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-brand-dark px-6 py-2.5 text-sm font-semibold text-white hover:bg-slate-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand-dark transition-all shadow-md hover:shadow-lg">
                        <svg class="w-4 h-4 text-brand-yellow" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                        Enregistrer la configuration
                    </button>
                </div>
            </form>
        </div>

        {{-- Carte de test --}}
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-heading font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    Tester l'envoi
                </h2>
                <p class="text-sm text-slate-500 mt-1">Envoie immédiatement un message de test à tous les numéros enregistrés ci-dessus.</p>
            </div>
            <form method="POST" action="{{ route('whatsapp.test') }}" class="shrink-0 w-full sm:w-auto">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-blue hover:border-brand-blue transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    Envoyer un message de test
                </button>
            </form>
        </div>
    </div>
</div>
@endsection