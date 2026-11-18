<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Bureau;
use App\Models\Etage;
use App\Models\Incident;
use App\Models\IncidentCommentaire;
use App\Models\TypeIncident;
use App\Models\User;
use App\Services\WhatsappService;
use App\Support\Activity;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $incidents = Incident::query()
            ->with(['bureau.etage', 'type', 'declarant', 'assigne'])
            ->when(! $user->isAdmin() && ! $user->isMaintenance(), fn ($q) => $q->where('user_id', $user->id))
            ->when($user->isMaintenance(), fn ($q) => $q->where('assigne_a', $user->id))
            ->filtre($request->only(['du', 'au', 'etage_id', 'bureau_id', 'type_incident_id', 'statut', 'priorite', 'q']))
            ->orderByRaw("case priorite when 'critique' then 1 when 'haute' then 2 when 'moyenne' then 3 else 4 end")
            ->latest()
            ->paginate(20)->withQueryString();

        return view('incidents.index', [
            'incidents' => $incidents,
            'etages' => Etage::orderBy('ordre')->get(),
            'bureaux' => Bureau::orderBy('numero')->get(['id', 'numero', 'libelle']),
            'types' => TypeIncident::orderBy('nom')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('incidents.create', [
            'bureaux' => Bureau::with('etage')->orderBy('numero')->get(),
            'types' => TypeIncident::orderBy('nom')->get(),
            'biens' => Bien::orderBy('designation')->get(['id', 'designation', 'bureau_id', 'numero_serie']),
            'bureauId' => $request->query('bureau_id'),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'bureau_id' => ['required', 'exists:bureaux,id'],
            'bien_id' => ['nullable', 'exists:biens,id'],
            'type_incident_id' => ['required', 'exists:types_incidents,id'],
            'description' => ['required', 'string', 'min:10', 'max:3000'],
            'priorite' => ['required', Rule::in(array_keys(config('intendance.priorites')))],
            'photo' => ['nullable', 'image', 'max:5120'],
        ]);

        if (! empty($data['bien_id'])) {
            abort_unless(Bien::whereKey($data['bien_id'])->where('bureau_id', $data['bureau_id'])->exists(), 422, 'Le bien choisi ne se trouve pas dans ce bureau.');
        }

        $chemin = $request->hasFile('photo') ? $request->file('photo')->store('incidents', 'public') : null;

        $incident = Incident::create([
            'reference' => Incident::genererReference(),
            'bureau_id' => $data['bureau_id'],
            'bien_id' => $data['bien_id'] ?? null,
            'type_incident_id' => $data['type_incident_id'],
            'user_id' => $request->user()->id,
            'description' => $data['description'],
            'priorite' => $data['priorite'],
            'statut' => 'nouveau',
            'photo' => $chemin,
        ]);

        Activity::log('incident_cree', $incident, "Signalement {$incident->reference}");

        // Notification WhatsApp asynchrone (Laravel Queues) — n'empêche jamais l'enregistrement du signalement
        try {
            WhatsappService::notifierNouvelIncident($incident);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('incidents.show', $incident)->with('success', "Incident {$incident->reference} signalé. Les administrateurs ont été alertés.");
    }

    public function show(Request $request, Incident $incident)
    {
        $this->autoriser($request->user(), $incident);

        $incident->load(['bureau.etage', 'bien', 'type', 'declarant', 'assigne', 'commentaires.user', 'notifications']);

        return view('incidents.show', [
            'incident' => $incident,
            'techniciens' => User::where('role', 'maintenance')->where('active', true)->orderBy('name')->get(),
        ]);
    }

    /** Traitement : statut, assignation, commentaire (BF-INC-07). */
    public function update(Request $request, Incident $incident)
    {
        $user = $request->user();
        $this->autoriser($user, $incident);
        abort_unless($user->isAdmin() || $user->isMaintenance(), 403);

        $regles = [
            'statut' => ['required', Rule::in(array_keys(config('intendance.statuts_incident')))],
            'commentaire' => ['nullable', 'string', 'max:2000'],
            'assigne_a' => ['nullable', Rule::exists('users', 'id')->where('role', 'maintenance')],
        ];
        $data = $request->validate($regles);

        if ($user->isMaintenance() && ! in_array($data['statut'], ['en_cours', 'resolu', $incident->statut], true)) {
            abort(403, 'Un agent de maintenance ne peut que démarrer ou résoudre un incident.');
        }

        $ancienStatut = $incident->statut;
        $changements = [];

        if ($user->isAdmin() && array_key_exists('assigne_a', $data) && (int) $data['assigne_a'] !== (int) $incident->assigne_a) {
            $incident->assigne_a = $data['assigne_a'] ?: null;
            $changements[] = 'assignation modifiée';
        }

        $incident->statut = $data['statut'];
        $incident->resolu_le = $data['statut'] === 'resolu' ? ($incident->resolu_le ?? now()) : null;
        $incident->save();

        if ($ancienStatut !== $data['statut'] || ! empty($data['commentaire']) || $changements) {
            IncidentCommentaire::create([
                'incident_id' => $incident->id,
                'user_id' => $user->id,
                'contenu' => trim(($data['commentaire'] ?? '').($changements ? "\n(".implode(', ', $changements).')' : '')) ?: null,
                'ancien_statut' => $ancienStatut !== $data['statut'] ? $ancienStatut : null,
                'nouveau_statut' => $ancienStatut !== $data['statut'] ? $data['statut'] : null,
            ]);
        }

        Activity::log('incident_traite', $incident, "{$incident->reference} : {$ancienStatut} → {$incident->statut}");

        return back()->with('success', 'Incident mis à jour.');
    }

    private function autoriser(User $user, Incident $incident): void
    {
        $ok = $user->isAdmin()
            || $incident->user_id === $user->id
            || ($user->isMaintenance() && $incident->assigne_a === $user->id);

        abort_unless($ok, 403, "Vous n'avez pas accès à cet incident.");
    }
}
