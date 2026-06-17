<?php
// app/Models/Sasepropi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sasepropi extends Model
{
    use HasFactory;
    protected $table    = 'sasepropi';
    protected $fillable = ['tipoopi', 'NumeroD', 'NroSerial','NroLinea','NroLineaC', 'CodItem', 'CodUbic','fk_sucursal', 'compraprov'];

    public function operacion()
    {
        return $this->belongsTo(Saopei::class, 'NumeroD', 'NumeroD')
            ->whereColumn('tipoopi', 'sasepropi.tipoopi');
    }

    public function producto()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'CodItem', 'codprod')->where('comercial', $comercial);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
}
