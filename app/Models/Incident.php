<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Incident extends Model
{
    protected $fillable = [
        'reference', 'bureau_id', 'bien_id', 'type_incident_id', 'user_id', 'assigne_a',
        'description', 'priorite', 'statut', 'photo', 'resolu_le', 'declarant_nom',
    'declarant_telephone',
    ];

    protected function casts(): array
    {
        return ['resolu_le' => 'datetime'];
    }

    public function bureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class)->withTrashed();
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(Bien::class)->withTrashed();
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(TypeIncident::class, 'type_incident_id');
    }

    public function declarant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function assigne(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigne_a');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(IncidentCommentaire::class)->oldest();
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function prioriteLabel(): string
    {
        return config('intendance.priorites.'.$this->priorite, $this->priorite);
    }

    public function statutLabel(): string
    {
        return config('intendance.statuts_incident.'.$this->statut, $this->statut);
    }

    public static function genererReference(): string
    {
        $annee = now()->format('Y');
        $dernier = static::whereYear('created_at', $annee)->count() + 1;

        do {
            $ref = sprintf('INC-%s-%05d', $annee, $dernier++);
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    /** Filtres avancés : du, au, etage_id, bureau_id, type_incident_id, statut, priorite, q */
    public function scopeFiltre(Builder $q, array $f): Builder
    {
        return $q
            ->when($f['du'] ?? null, fn ($q, $v) => $q->whereDate('incidents.created_at', '>=', $v))
            ->when($f['au'] ?? null, fn ($q, $v) => $q->whereDate('incidents.created_at', '<=', $v))
            ->when($f['bureau_id'] ?? null, fn ($q, $v) => $q->where('incidents.bureau_id', $v))
            ->when($f['etage_id'] ?? null, fn ($q, $v) => $q->whereHas('bureau', fn ($b) => $b->where('etage_id', $v)))
            ->when($f['type_incident_id'] ?? null, fn ($q, $v) => $q->where('incidents.type_incident_id', $v))
            ->when($f['statut'] ?? null, fn ($q, $v) => $q->where('incidents.statut', $v))
            ->when($f['priorite'] ?? null, fn ($q, $v) => $q->where('incidents.priorite', $v))
            ->when($f['q'] ?? null, fn ($q, $v) => $q->where(fn ($w) => $w
                ->where('incidents.reference', 'like', "%$v%")
                ->orWhere('incidents.description', 'like', "%$v%")));
    }

    public function scopeOuverts(Builder $q): Builder
    {
        return $q->whereIn('statut', ['nouveau', 'en_cours']);
    }
}
