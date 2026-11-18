<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bien extends Model
{
    use SoftDeletes;

    protected $table = 'biens';

    protected $fillable = ['designation', 'categorie_id', 'numero_serie', 'etat', 'bureau_id', 'date_acquisition', 'remarques'];

    protected function casts(): array
    {
        return ['date_acquisition' => 'date'];
    }

    public function categorie(): BelongsTo
    {
        return $this->belongsTo(CategorieBien::class, 'categorie_id');
    }

    public function bureau(): BelongsTo
    {
        return $this->belongsTo(Bureau::class)->withTrashed();
    }

    public function historiques(): HasMany
    {
        return $this->hasMany(BienHistorique::class)->latest();
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function etatLabel(): string
    {
        return config('intendance.etats_bien.'.$this->etat, $this->etat);
    }
}
