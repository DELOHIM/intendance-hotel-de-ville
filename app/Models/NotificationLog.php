<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = ['incident_id', 'destinataire', 'message', 'statut', 'tentatives', 'erreur', 'envoye_le'];

    protected function casts(): array
    {
        return ['envoye_le' => 'datetime'];
    }

    public function incident(): BelongsTo
    {
        return $this->belongsTo(Incident::class);
    }
}
