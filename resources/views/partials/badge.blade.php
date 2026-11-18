@php
    // Définition des palettes de couleurs (versions améliorées avec bordures)
    $palette = [
        'priorite' => [
            'faible'   => 'bg-slate-50 text-slate-600 border-slate-200',
            'moyenne'  => 'bg-blue-50 text-blue-700 border-blue-200',
            'haute'    => 'bg-orange-50 text-orange-700 border-orange-200',
            'critique' => 'bg-red-50 text-brand-red border-red-200',
        ],
        'statut' => [
            'nouveau'  => 'bg-blue-50 text-blue-700 border-blue-200',
            'en_cours' => 'bg-amber-50 text-amber-700 border-amber-200',
            'resolu'   => 'bg-green-50 text-green-700 border-green-200',
            'rejete'   => 'bg-slate-100 text-slate-600 border-slate-200',
        ],
        'etat' => [
            'bon'            => 'bg-green-50 text-green-700 border-green-200',
            'en_panne'       => 'bg-red-50 text-brand-red border-red-200',
            'en_reparation'  => 'bg-amber-50 text-amber-700 border-amber-200',
            'hors_service'   => 'bg-slate-100 text-slate-700 border-slate-200',
            'reforme'        => 'bg-slate-200 text-slate-600 border-slate-300',
        ],
        'notif' => [
            'envoye'     => 'bg-green-50 text-green-700 border-green-200',
            'en_attente' => 'bg-amber-50 text-amber-700 border-amber-200',
            'echec'      => 'bg-red-50 text-brand-red border-red-200',
        ],
    ];

    // Définition des points de couleur (pour le style "pillule")
    $dots = [
        'priorite' => [
            'faible'   => 'bg-slate-400',
            'moyenne'  => 'bg-blue-500',
            'haute'    => 'bg-orange-500',
            'critique' => 'bg-brand-red',
        ],
        'statut' => [
            'nouveau'  => 'bg-blue-500',
            'en_cours' => 'bg-amber-500',
            'resolu'   => 'bg-green-500',
            'rejete'   => 'bg-slate-400',
        ],
        'etat' => [
            'bon'            => 'bg-green-500',
            'en_panne'       => 'bg-brand-red',
            'en_reparation'  => 'bg-amber-500',
            'hors_service'   => 'bg-slate-500',
            'reforme'        => 'bg-slate-600',
        ],
        'notif' => [
            'envoye'     => 'bg-green-500',
            'en_attente' => 'bg-amber-500',
            'echec'      => 'bg-brand-red',
        ],
    ];

    // Libellés (récupérés depuis la config ou définis en dur)
    $libelles = [
        'priorite' => config('intendance.priorites'),
        'statut'   => config('intendance.statuts_incident'),
        'etat'     => config('intendance.etats_bien'),
        'notif'    => ['envoye' => 'Envoyé', 'en_attente' => 'En attente', 'echec' => 'Échec'],
    ];

    // Récupération des classes avec valeurs par défaut sécurisées
    $classe = $palette[$type][$value] ?? 'bg-slate-50 text-slate-600 border-slate-200';
    $dot = $dots[$type][$value] ?? 'bg-slate-400';
    $label = $libelles[$type][$value] ?? $value;
@endphp

<span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold border {{ $classe }} shadow-sm">
    {{-- Point de couleur --}}
    <span class="w-1.5 h-1.5 rounded-full {{ $dot }} shrink-0"></span>
    {{-- Libellé --}}
    <span>{{ $label }}</span>
</span>