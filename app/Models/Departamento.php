<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $primaryKey = 'id';

    protected $fillable = [
        'Nome',
    ];

    protected $casts = [
        'Nome' => 'string',
    ];

    public function cargos(): HasMany
    {
        return $this->hasMany(Cargo::class, 'id_departamento');
    }
}
