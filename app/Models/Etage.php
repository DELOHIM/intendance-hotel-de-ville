<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etage extends Model
{
    protected $fillable = ['nom', 'ordre'];

    public function bureaux(): HasMany
    {
        return $this->hasMany(Bureau::class);
    }
}
