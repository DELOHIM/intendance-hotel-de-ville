<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bureau extends Model
{
    use SoftDeletes;

    protected $table = 'bureaux';

    protected $fillable = ['numero', 'libelle', 'etage_id', 'service', 'description'];

    protected static function booted(): void
    {
        // Le numéro est non modifiable après création (BF-BUR-02)
        static::updating(function (Bureau $bureau) {
            if ($bureau->isDirty('numero')) {
                $bureau->numero = $bureau->getOriginal('numero');
            }
        });
    }

    public function etage(): BelongsTo
    {
        return $this->belongsTo(Etage::class);
    }

    public function biens(): HasMany
    {
        return $this->hasMany(Bien::class);
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }

    public function agents(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bureau_user')->withTimestamps();
    }

    /** URL encodée dans le QR code : redirige vers la fiche après authentification. */
    public function scanUrl(): string
    {
        return route('bureaux.scan', $this->numero);
    }
}
