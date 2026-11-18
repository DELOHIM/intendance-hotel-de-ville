<?php

use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BienController;
use App\Http\Controllers\BureauController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\IncidentController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicIncidentController;
use App\Http\Controllers\RapportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsappController;
use Illuminate\Support\Facades\Route;

// ---------- Page publique (Connexion + bouton de signalement) ----------
Route::get('/', [AuthController::class, 'showLogin'])->name('home')->middleware('guest');

// Formulaire de signalement public (accessible sans connexion)
Route::post('/signaler', [PublicIncidentController::class, 'store'])
    ->middleware('throttle:5,1')
    ->name('public.incidents.store');

// ---------- Invités ----------
Route::middleware('guest')->group(function () {
    Route::get('/connexion', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/connexion', [AuthController::class, 'login'])->middleware('throttle:8,1')->name('login.post');

    Route::get('/mot-de-passe-oublie', [AuthController::class, 'showForgot'])->name('password.request');
    Route::post('/mot-de-passe-oublie', [AuthController::class, 'sendResetLink'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reinitialiser-mot-de-passe/{token}', [AuthController::class, 'showReset'])->name('password.reset');
    Route::post('/reinitialiser-mot-de-passe', [AuthController::class, 'reset'])->name('password.update');
});

// ---------- Utilisateurs connectés ----------
Route::middleware('auth')->group(function () {
    Route::post('/deconnexion', [AuthController::class, 'logout'])->name('logout');
    
    // Tableau de bord (déplacé de / vers /tableau-de-bord)
    Route::get('/tableau-de-bord', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profil', [ProfileController::class, 'update'])->name('profile.update');

    // Scan QR code
    Route::get('/b/{numero}', [BureauController::class, 'scan'])->name('bureaux.scan');

    // Incidents
    Route::get('/incidents', [IncidentController::class, 'index'])->name('incidents.index');
    Route::get('/incidents/nouveau', [IncidentController::class, 'create'])->name('incidents.create')->middleware('role:admin,agent');
    Route::post('/incidents', [IncidentController::class, 'store'])->name('incidents.store')->middleware('role:admin,agent');
    Route::get('/incidents/{incident}', [IncidentController::class, 'show'])->name('incidents.show');
    Route::put('/incidents/{incident}', [IncidentController::class, 'update'])->name('incidents.update')->middleware('role:admin,maintenance');

    // Bureaux (consultation)
    Route::middleware('role:admin,agent')->group(function () {
        Route::get('/bureaux', [BureauController::class, 'index'])->name('bureaux.index');
    });

    // ---------- Administrateurs ----------
    Route::middleware('role:admin')->group(function () {
        Route::get('/bureaux/creer', [BureauController::class, 'create'])->name('bureaux.create');
        Route::post('/bureaux', [BureauController::class, 'store'])->name('bureaux.store');
        Route::get('/bureaux/planche-qr', [BureauController::class, 'planche'])->name('bureaux.planche');
        Route::get('/bureaux/{bureau}/modifier', [BureauController::class, 'edit'])->name('bureaux.edit');
        Route::put('/bureaux/{bureau}', [BureauController::class, 'update'])->name('bureaux.update');
        Route::delete('/bureaux/{bureau}', [BureauController::class, 'destroy'])->name('bureaux.destroy');
        Route::post('/bureaux/{bureau}/restaurer', [BureauController::class, 'restore'])->withTrashed()->name('bureaux.restore');
        Route::get('/bureaux/{bureau}/qr', [BureauController::class, 'qr'])->name('bureaux.qr');
        Route::get('/bureaux/{bureau}/qr-pdf', [BureauController::class, 'qrPdf'])->name('bureaux.qr.pdf');

        Route::get('/biens/export', [BienController::class, 'export'])->name('biens.export');
        Route::post('/biens/{bien}/transfert', [BienController::class, 'transfer'])->name('biens.transfer');
        Route::resource('biens', BienController::class);

        Route::get('/rapports', [RapportController::class, 'index'])->name('rapports.index');
        Route::get('/rapports/inventaire', [RapportController::class, 'inventaire'])->name('rapports.inventaire');
        Route::get('/rapports/incidents', [RapportController::class, 'incidents'])->name('rapports.incidents');
        Route::get('/rapports/historique', [RapportController::class, 'historique'])->name('rapports.historique');

        Route::get('/parametres', [ParametreController::class, 'index'])->name('parametres.index');
        Route::post('/parametres/{kind}', [ParametreController::class, 'store'])->name('parametres.store');
        Route::put('/parametres/{kind}/{id}', [ParametreController::class, 'update'])->name('parametres.update');
        Route::delete('/parametres/{kind}/{id}', [ParametreController::class, 'destroy'])->name('parametres.destroy');

        Route::get('/whatsapp', [WhatsappController::class, 'edit'])->name('whatsapp.edit');
        Route::put('/whatsapp', [WhatsappController::class, 'update'])->name('whatsapp.update');
        Route::post('/whatsapp/test', [WhatsappController::class, 'test'])->name('whatsapp.test');
        Route::get('/notifications', [WhatsappController::class, 'journal'])->name('notifications.index');
        Route::post('/notifications/{log}/renvoyer', [WhatsappController::class, 'retry'])->name('notifications.retry');
    });

    // Fiche bureau
    Route::get('/bureaux/{bureau}', [BureauController::class, 'show'])->name('bureaux.show')->middleware('role:admin,agent');

    // ---------- Super administrateur ----------
    Route::middleware('role:super_admin')->group(function () {
        Route::get('/utilisateurs', [UserController::class, 'index'])->name('users.index');
        Route::get('/utilisateurs/creer', [UserController::class, 'create'])->name('users.create');
        Route::post('/utilisateurs', [UserController::class, 'store'])->name('users.store');
        Route::get('/utilisateurs/{user}/modifier', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/utilisateurs/{user}', [UserController::class, 'update'])->name('users.update');
        Route::post('/utilisateurs/{user}/basculer', [UserController::class, 'toggle'])->name('users.toggle');

        Route::get('/journal-activite', [ActivityLogController::class, 'index'])->name('logs.index');
    });
});