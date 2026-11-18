<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TypeIncident extends Model
{
    protected $table = 'types_incidents';

    protected $fillable = ['nom'];

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class, 'type_incident_id');
    }
}
