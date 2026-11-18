<?php

use Illuminate\Support\Facades\Schedule;

// Sauvegarde automatique quotidienne de la base de données (BNF-07)
Schedule::command('db:backup')->dailyAt('02:00');

// Traitement de la file d'attente (alternative à un worker permanent : cron "* * * * * php artisan schedule:run")
Schedule::command('queue:work --stop-when-empty --max-time=55')->everyMinute()->withoutOverlapping();
