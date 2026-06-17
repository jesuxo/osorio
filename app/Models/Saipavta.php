<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saipavta extends Model
{
    use HasFactory;
    protected $table    = 'saipavta';
    protected $fillable = ['TipoFac', 'NumeroD', 'CodPago', 'dolares','monto','pesos', 'RetencT', 'Impuesto', 'FechaE', 'Descrip'];
    
    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function satarj  (){
        return $this->belongsTo(Satarj::class, 'codpago', 'codtarj');
    }
}
