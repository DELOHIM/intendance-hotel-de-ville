{{-- Message de succès --}}
@if (session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
         class="mb-4 rounded-xl border border-green-200 bg-green-50 p-4 text-sm text-green-800 shadow-sm flex items-start gap-3 relative">
        <div class="p-1.5 rounded-full bg-green-100 text-green-600 shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
        </div>
        <div class="flex-1 pr-6">{{ session('success') }}</div>
        <button @click="show = false" class="absolute top-3 right-3 text-green-500 hover:text-green-700 transition-colors" title="Fermer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

{{-- Message d'erreur (session) --}}
@if (session('error'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
         class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm flex items-start gap-3 relative">
        <div class="p-1.5 rounded-full bg-red-100 text-brand-red shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
        </div>
        <div class="flex-1 pr-6">{{ session('error') }}</div>
        <button @click="show = false" class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition-colors" title="Fermer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

{{-- Message de statut (ex: mot de passe réinitialisé) --}}
@if (session('status'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
         class="mb-4 rounded-xl border border-blue-200 bg-blue-50 p-4 text-sm text-blue-800 shadow-sm flex items-start gap-3 relative">
        <div class="p-1.5 rounded-full bg-blue-100 text-brand-blue shrink-0 mt-0.5">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <div class="flex-1 pr-6">{{ session('status') }}</div>
        <button @click="show = false" class="absolute top-3 right-3 text-blue-500 hover:text-blue-700 transition-colors" title="Fermer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
@endif

{{-- Erreurs de validation (hors login / password) --}}
@if ($errors->any() && ! request()->routeIs('login', 'password.*'))
    <div x-data="{ show: true }" x-show="show" x-transition.opacity.duration.500ms
         class="mb-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800 shadow-sm relative">
        <div class="flex items-start gap-3">
            <div class="p-1.5 rounded-full bg-red-100 text-brand-red shrink-0 mt-0.5">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="flex-1 pr-6">
                <h3 class="font-bold mb-2">Veuillez corriger les erreurs suivantes :</h3>
                <ul class="space-y-1.5">
                    @foreach ($errors->all() as $e)
                        <li class="flex items-start gap-2">
                            <span class="w-1.5 h-1.5 rounded-full bg-brand-red shrink-0 mt-1.5"></span>
                            <span>{{ $e }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <button @click="show = false" class="absolute top-3 right-3 text-red-500 hover:text-red-700 transition-colors" title="Fermer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>
@endif