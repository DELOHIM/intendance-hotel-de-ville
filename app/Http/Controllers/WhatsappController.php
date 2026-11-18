<?php

namespace App\Http\Controllers;

use App\Jobs\SendWhatsappMessage;
use App\Models\NotificationLog;
use App\Models\Setting;
use App\Services\WhatsappService;
use App\Support\Activity;
use Illuminate\Http\Request;

class WhatsappController extends Controller
{
    public function edit()
    {
        return view('parametres.whatsapp', [
            'numeros' => implode("\n", WhatsappService::numeros()),
            'template' => WhatsappService::template(),
            'configure' => filled(config('services.green_api.id_instance')) && filled(config('services.green_api.token')),
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'numeros' => ['nullable', 'string', 'max:2000'],
            'template' => ['required', 'string', 'max:2000'],
        ]);

        Setting::set('whatsapp_numeros', $data['numeros'] ?? '');
        Setting::set('whatsapp_template', $data['template']);
        Activity::log('whatsapp_config', null, 'Configuration WhatsApp modifiée');

        return back()->with('success', 'Configuration WhatsApp enregistrée.');
    }

    /** Envoi d'un message de test synchrone au premier numéro configuré. */
    public function test()
    {
        $numeros = WhatsappService::numeros();

        if (! $numeros) {
            return back()->with('error', 'Aucun numéro configuré.');
        }

        $resultats = [];
        foreach ($numeros as $numero) {
            try {
                WhatsappService::envoyer($numero, "✅ Test de notification - Intendance Hôtel de Ville de Kinshasa\n".now()->format('d/m/Y H:i'));
                $resultats[] = "$numero : envoyé";
            } catch (\Throwable $e) {
                $resultats[] = "$numero : ÉCHEC ({$e->getMessage()})";
            }
        }

        return back()->with('success', implode(' | ', $resultats));
    }

    /** Journal des envois (BF-NOT-05). */
    public function journal(Request $request)
    {
        $logs = NotificationLog::with('incident')
            ->when($request->statut, fn ($q, $v) => $q->where('statut', $v))
            ->latest()->paginate(25)->withQueryString();

        return view('parametres.journal', compact('logs'));
    }

    public function retry(NotificationLog $log)
    {
        $log->update(['statut' => 'en_attente', 'erreur' => null]);
        SendWhatsappMessage::dispatch($log->id);

        return back()->with('success', 'Renvoi mis en file d\'attente.');
    }
}
