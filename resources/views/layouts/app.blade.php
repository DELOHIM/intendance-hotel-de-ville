<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Tableau de bord') — Intendance Hôtel de Ville</title>
    
    <!-- Polices Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        heading: ['Poppins', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            blue: '#007FFF', // Bleu ciel RDC
                            yellow: '#F7D618', // Jaune RDC
                            red: '#CE1021', // Rouge RDC
                            dark: '#0f172a', // Slate 900
                        }
                    }
                }
            }
        }
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('head')
    <style>
        [x-cloak]{display:none!important}
        /* Scrollbar personnalisée pour la sidebar */
        .sidebar-scroll::-webkit-scrollbar { width: 6px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #475569; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen font-sans antialiased">
@php
    $u = auth()->user();
    $nouveaux = $u->isAdmin() ? \App\Models\Incident::where('statut', 'nouveau')->count() : 0;
    
    // Définition des menus avec leurs icônes SVG (Heroicons)
    $menu = [];
    $menu[] = ['dashboard', 'Tableau de bord', 'dashboard', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>'];
    
    if ($u->isAdmin() || $u->role === 'agent') {
        $menu[] = ['bureaux.index', 'Bureaux', 'bureaux.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>'];
    }
    if ($u->isAdmin()) {
        $menu[] = ['biens.index', 'Biens', 'biens.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>'];
    }
    $menu[] = ['incidents.index', $u->isMaintenance() ? 'Mes interventions' : 'Incidents', 'incidents.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'];
    if ($u->isAdmin()) {
        $menu[] = ['rapports.index', 'Rapports', 'rapports.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'];
        $menu[] = ['parametres.index', 'Paramétrage', 'parametres.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>'];
        $menu[] = ['whatsapp.edit', 'WhatsApp', 'whatsapp.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>'];
        $menu[] = ['notifications.index', 'Journal des envois', 'notifications.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>'];
    }
    if ($u->isSuperAdmin()) {
        $menu[] = ['users.index', 'Utilisateurs', 'users.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>'];
        $menu[] = ['logs.index', 'Journal d\'activité', 'logs.*', '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'];
    }
@endphp

<div x-data="{ open: false }" class="flex h-screen overflow-hidden bg-slate-50">
    {{-- Overlay mobile --}}
    <div x-show="open" x-cloak @click="open=false" class="fixed inset-0 z-20 bg-slate-900/50 backdrop-blur-sm md:hidden transition-opacity"></div>

    {{-- Barre latérale (Sidebar) --}}
    <aside class="fixed inset-y-0 left-0 z-30 w-72 transform bg-slate-900 text-slate-300 transition-transform duration-300 ease-in-out md:static md:translate-x-0 flex flex-col shadow-2xl"
           :class="open ? 'translate-x-0' : '-translate-x-full'">
        
        {{-- Logo & Titre --}}
        <div class="flex items-center gap-4 px-6 py-6 border-b border-slate-800/50 bg-slate-900">
            <!-- Remplacez le chemin par le vôtre -->
            <img src="{{ asset('images/logo-kinshasa.png') }}" alt="Logo Ville de Kinshasa" class="h-12 w-auto object-contain bg-white rounded-full p-1 shadow-sm">
            <div>
                <h1 class="font-heading text-lg font-bold text-white tracking-wide leading-tight">Hôtel de Ville</h1>
                <p class="text-xs text-brand-yellow font-medium tracking-wider uppercase mt-0.5">Kinshasa</p>
            </div>
        </div>

        {{-- Menu Navigation --}}
        <nav class="flex-1 overflow-y-auto sidebar-scroll px-4 py-6 space-y-1.5">
            <p class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">Menu Principal</p>
            
            @foreach ($menu as [$route, $label, $pattern, $icon])
                @php $isActive = request()->routeIs($pattern); @endphp
                <a href="{{ route($route) }}"
                   class="group flex items-center justify-between rounded-xl px-3 py-2.5 transition-all duration-200 
                          {{ $isActive ? 'bg-brand-blue text-white shadow-md shadow-brand-blue/20' : 'hover:bg-slate-800/80 hover:text-white' }}">
                    <div class="flex items-center gap-3">
                        <span class="transition-transform duration-200 group-hover:scale-110 {{ $isActive ? 'text-white' : 'text-slate-400 group-hover:text-brand-yellow' }}">
                            {!! $icon !!}
                        </span>
                        <span class="font-medium text-sm">{{ $label }}</span>
                    </div>
                    @if ($route === 'incidents.index' && $nouveaux > 0)
                        <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-red text-[10px] font-bold text-white shadow-sm ring-2 ring-slate-900">
                            {{ $nouveaux }}
                        </span>
                    @endif
                </a>
            @endforeach
        </nav>

        {{-- Profil Utilisateur (Bas de la sidebar) --}}
        <div class="border-t border-slate-800/50 p-4 bg-slate-900/50">
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl p-2 hover:bg-slate-800 transition-colors group">
                <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-brand-blue to-brand-yellow flex items-center justify-center text-white font-bold text-lg shadow-inner">
                    {{ substr($u->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate group-hover:text-brand-yellow transition-colors">{{ $u->name }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $u->roleLabel() }}</p>
                </div>
                <svg class="w-4 h-4 text-slate-500 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
        </div>
    </aside>

    {{-- Contenu Principal --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        {{-- En-tête (Header) --}}
        <header class="sticky top-0 z-10 flex items-center justify-between border-b border-slate-200 bg-white/80 backdrop-blur-md px-4 py-4 sm:px-6 shadow-sm">
            <div class="flex items-center gap-4">
                <button @click="open = !open" class="md:hidden rounded-lg p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-700 transition-colors focus:outline-none focus:ring-2 focus:ring-brand-blue" aria-label="Menu">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                </button>
                <h2 class="font-heading text-xl font-semibold text-slate-800">@yield('title', 'Tableau de bord')</h2>
            </div>
            
            <div class="flex items-center gap-4 text-sm">
                @if ($nouveaux > 0)
                    <a href="{{ route('incidents.index', ['statut' => 'nouveau']) }}" class="group relative flex items-center gap-2 rounded-full bg-red-50 px-4 py-1.5 text-brand-red font-medium border border-red-100 hover:bg-red-100 transition-colors">
                        <span class="relative flex h-2 w-2">
                          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-red opacity-75"></span>
                          <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-red"></span>
                        </span>
                        {{ $nouveaux }} nouveau(x)
                    </a>
                @endif
                
                <div class="hidden sm:block h-6 w-px bg-slate-200 mx-1"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-1.5 text-slate-600 hover:bg-slate-50 hover:text-brand-red hover:border-red-200 transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span class="hidden md:inline">Déconnexion</span>
                    </button>
                </form>
            </div>
        </header>

        {{-- Zone de contenu défilante --}}
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <div class="max-w-7xl mx-auto">
                @include('partials.flash')
                @yield('content')
            </div>
        </main>
    </div>
</div>
@stack('scripts')
</body>
</html>