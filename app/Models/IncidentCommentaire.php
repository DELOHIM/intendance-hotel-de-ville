<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentCommentaire extends Model
{
    protected $table = 'incident_commentaires';

    protected $fillable = ['incident_id', 'user_id', 'contenu', 'ancien_statut', 'nouveau_statut'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
