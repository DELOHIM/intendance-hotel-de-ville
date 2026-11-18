<?php

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Services\WhatsappService;
use Illuminate\Http\Request;

class PublicIncidentController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'bureau_id' => 'required|exists:bureaux,id',
            'type_incident_id' => 'required|exists:types_incidents,id',
            'description' => 'required|string|min:10|max:3000',
            'priorite' => 'required|in:faible,moyenne,haute,critique',
            'declarant_nom' => 'required|string|max:255',
            'declarant_telephone' => 'nullable|string|max:20',
        ]);

        $incident = Incident::create([
            'reference' => Incident::genererReference(),
            'bureau_id' => $validated['bureau_id'],
            'type_incident_id' => $validated['type_incident_id'],
            'description' => $validated['description'],
            'priorite' => $validated['priorite'],
            'statut' => 'nouveau',
            'user_id' => null,
            'declarant_nom' => $validated['declarant_nom'],
            'declarant_telephone' => $validated['declarant_telephone'],
        ]);

        try {
            WhatsappService::notifierNouvelIncident($incident);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('login')
            ->with('success', "✅ Votre signalement a bien été enregistré. Référence : {$incident->reference}. Les administrateurs ont été notifiés par WhatsApp.");
    }
}