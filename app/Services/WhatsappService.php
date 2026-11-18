<?php

namespace App\Services;

use App\Jobs\SendWhatsappMessage;
use App\Models\Incident;
use App\Models\NotificationLog;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsappService
{
     public const DEFAULT_TEMPLATE = "🚨 *Nouvel incident {reference}*\n\n"
        ."📍 Bureau : {bureau} ({etage})\n"
        ."🔧 Type : {type}\n"
        ."⚠️ Priorité : {priorite}\n"
        ."📝 {description}\n"
        ."👤 Signalé par : {agent}\n"
        ."📞 Téléphone : {telephone}\n" // <-- Ajout
        ."🕒 {date}";

    /** Numéros administrateurs configurés (un par ligne ou séparés par virgule/point-virgule). */
    public static function numeros(): array
    {
        $brut = (string) Setting::get('whatsapp_numeros', '');

        return collect(preg_split('/[\s,;]+/', $brut))
            ->map(fn ($n) => trim($n))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function template(): string
    {
        return Setting::get('whatsapp_template') ?: self::DEFAULT_TEMPLATE;
    }

        public static function construireMessage(Incident $incident): string
    {
        $incident->loadMissing(['bureau.etage', 'type', 'declarant']);

        // Fallback pour les déclarants anonymes
        $nomDeclarant = $incident->declarant->name ?? $incident->declarant_nom ?? '-';
        $telephoneDeclarant = $incident->declarant->phone ?? $incident->declarant_telephone ?? 'Non fourni';

        return strtr(self::template(), [
            '{reference}' => $incident->reference,
            '{bureau}' => $incident->bureau->numero.' - '.$incident->bureau->libelle,
            '{etage}' => $incident->bureau->etage->nom ?? '-',
            '{type}' => $incident->type->nom ?? '-',
            '{priorite}' => $incident->prioriteLabel(),
            '{description}' => $incident->description,
            '{agent}' => $nomDeclarant,
            '{telephone}' => $telephoneDeclarant, // <-- Ajout
            '{date}' => $incident->created_at->format('d/m/Y H:i'),
        ]);
    }

    /** Crée un journal d'envoi par destinataire et met chaque envoi en file d'attente. */
    public static function notifierNouvelIncident(Incident $incident): void
    {
        $message = self::construireMessage($incident);

        foreach (self::numeros() as $numero) {
            $log = NotificationLog::create([
                'incident_id' => $incident->id,
                'destinataire' => $numero,
                'message' => $message,
                'statut' => 'en_attente',
            ]);

            SendWhatsappMessage::dispatch($log->id);
        }
    }

    public static function normaliserNumero(string $numero): string
    {
        $chiffres = preg_replace('/\D+/', '', $numero);
        $indicatif = (string) config('services.green_api.country_code', '243');

        if (str_starts_with($chiffres, '00')) {
            $chiffres = substr($chiffres, 2);
        } elseif (str_starts_with($chiffres, '0')) {
            $chiffres = $indicatif.substr($chiffres, 1);
        } elseif (! str_starts_with($numero, '+') && strlen($chiffres) <= 9) {
            $chiffres = $indicatif.$chiffres;
        }

        return $chiffres;
    }

    /** Envoi réel via Green API. Lève une exception en cas d'échec. */
    public static function envoyer(string $numero, string $message): string
    {
        $cfg = config('services.green_api');

        if (empty($cfg['id_instance']) || empty($cfg['token'])) {
            throw new RuntimeException('Green API non configurée (GREEN_API_ID_INSTANCE / GREEN_API_TOKEN dans .env).');
        }

        $url = rtrim($cfg['url'], '/')."/waInstance{$cfg['id_instance']}/sendMessage/{$cfg['token']}";

        $reponse = Http::timeout(20)->acceptJson()->post($url, [
            'chatId' => self::normaliserNumero($numero).'@c.us',
            'message' => $message,
        ]);

        if (! $reponse->successful()) {
            throw new RuntimeException('Green API HTTP '.$reponse->status().' : '.$reponse->body());
        }

        return (string) ($reponse->json('idMessage') ?? '');
    }
}
