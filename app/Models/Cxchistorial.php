<?php
// app/Models/Cxchistorial.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cxchistorial extends Model
{

    protected $table = 'cxchistorial';

    protected $fillable = [
        'cliente_codclie',
        'viaje_moto_id',
        'viaje_id',
        'modelo_moto',
        'cantidad',
        'monto',
        'tipo',
        'fecha_hora',
        'usuario_id',
        'observaciones'
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'monto' => 'decimal:2'
    ];

    public function cliente()
    {
        return $this->belongsTo(Saclie::class, 'cliente_codclie', 'codclie');
    }

    public function viaje(): BelongsTo
    {
        return $this->belongsTo(Cwviaje::class, 'viaje_id');
    }

    public function viajeMoto(): BelongsTo
    {
        return $this->belongsTo(Cwviajemoto::class, 'viaje_moto_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
