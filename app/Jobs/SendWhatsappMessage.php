<?php

namespace App\Jobs;

use App\Models\NotificationLog;
use App\Services\WhatsappService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 4;

    public function __construct(public int $notificationLogId)
    {
    }

    /** Délais entre retentatives (secondes). */
    public function backoff(): array
    {
        return [30, 120, 600];
    }

    public function handle(): void
    {
        $log = NotificationLog::find($this->notificationLogId);

        if (! $log || $log->statut === 'envoye') {
            return;
        }

        $log->increment('tentatives');

        try {
            WhatsappService::envoyer($log->destinataire, $log->message);
            $log->update(['statut' => 'envoye', 'envoye_le' => now(), 'erreur' => null]);
        } catch (Throwable $e) {
            $log->update(['erreur' => $e->getMessage()]);
            Log::warning('Echec WhatsApp #'.$log->id.' : '.$e->getMessage());
            throw $e; // déclenche la retentative automatique
        }
    }

    public function failed(Throwable $e): void
    {
        NotificationLog::whereKey($this->notificationLogId)->update([
            'statut' => 'echec',
            'erreur' => $e->getMessage(),
        ]);
        Log::error('WhatsApp définitivement en échec #'.$this->notificationLogId.' : '.$e->getMessage());
    }
}
