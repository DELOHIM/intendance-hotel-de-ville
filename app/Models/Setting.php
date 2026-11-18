<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'cle';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['cle', 'valeur'];

    public static function get(string $cle, mixed $default = null): mixed
    {
        return static::find($cle)?->valeur ?? $default;
    }

    public static function set(string $cle, ?string $valeur): void
    {
        static::updateOrCreate(['cle' => $cle], ['valeur' => $valeur]);
    }
}
