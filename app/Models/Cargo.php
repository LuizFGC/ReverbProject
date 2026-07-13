<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cargo extends Model
{
    protected $table = 'cargos';

    protected $primaryKey = 'id';
    protected $fillable = [
        'Nome',
        'id_departamento',
    ];

    protected $casts = [
        'Nome' => 'string',
        'id_departamento' => 'integer',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'id_departamento');
    }

    public function funcionarios(): HasMany
    {
        return $this->hasMany(Funcionario::class, 'cargo_id');
    }
}
