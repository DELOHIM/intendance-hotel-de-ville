<?php

return [
    'roles' => [
        'super_admin' => 'Super Administrateur',
        'admin' => 'Administrateur',
        'agent' => 'Agent',
        'maintenance' => 'Agent de maintenance',
    ],
    'priorites' => [
        'faible' => 'Faible',
        'moyenne' => 'Moyenne',
        'haute' => 'Haute',
        'critique' => 'Critique',
    ],
    'statuts_incident' => [
        'nouveau' => 'Nouveau',
        'en_cours' => 'En cours',
        'resolu' => 'Résolu',
        'rejete' => 'Rejeté',
    ],
    'etats_bien' => [
        'bon' => 'Bon',
        'en_panne' => 'En panne',
        'en_reparation' => 'En réparation',
        'hors_service' => 'Hors service',
        'reforme' => 'Réformé',
    ],
    'backup' => [
        'path' => env('BACKUP_PATH', 'storage/app/backups'),
        'keep_days' => (int) env('BACKUP_KEEP_DAYS', 14),
    ],
];
