<?php
// app/Models/Saseprcom.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saseprcom extends Model
{
    use HasFactory;
    protected $table    = 'saseprcom';
    protected $fillable = [
        'codsucu', 'tipocom', 'numerod', 'codprov', 'nroLinea', 'nrolineac',
        'nroserial', 'coditem', 'codubic', 'fk_sucursal',
        'checked', 'check_comment', 'checked_by', 'checked_at', 'check_status'
    ];

    protected $casts = [
        'checked_at' => 'datetime',
        'checked' => 'integer'
    ];

    public function producto()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'coditem', 'codprod')->where('comercial', $comercial);
    }

    public function compra()
    {
        return $this->belongsTo(Sacomp::class, 'numerod', 'numerod')
            ->whereColumn('tipocom', 'saseprcom.tipocom');
    }

    public function sucursal()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function checker()
    {
        return $this->belongsTo(User::class, 'checked_by', 'id');
    }

    public function getStatusTextAttribute()
    {
        return match($this->checked) {
            0 => 'Pendiente',
            1 => 'Verificado',
            2 => 'Descargado',
            3 => 'Vendido',
            default => 'Desconocido'
        };
    }

    public function getStatusColorAttribute()
    {
        return match($this->checked) {
            0 => 'warning',
            1 => 'success',
            2 => 'danger',
            3 => 'primary',
            default => 'secondary'
        };
    }

    public function getStatusIconAttribute()
    {
        return match($this->checked) {
            0 => 'bi-clock-history',
            1 => 'bi-check-circle',
            2 => 'bi-arrow-down-circle',
            3 => 'bi-cart-check',
            default => 'bi-question-circle'
        };
    }
}
