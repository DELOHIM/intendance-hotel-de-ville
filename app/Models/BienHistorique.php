<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BienHistorique extends Model
{
    protected $table = 'bien_historiques';

    protected $fillable = ['bien_id', 'user_id', 'action', 'ancien_bureau_id', 'nouveau_bureau_id', 'details'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function ancienBureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class, 'ancien_bureau_id')->withTrashed();
    }

    public function nouveauBureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class, 'nouveau_bureau_id')->withTrashed();
    }
}
