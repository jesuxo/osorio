<?php
// app/Models/Saseprfac.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saseprfac extends Model
{
    use HasFactory;
    protected $table    = 'saseprfac';
    protected $fillable = ['tipofac', 'numerod', 'nrolinea', 'nrolineac', 'nroserial', 'coditem', 'codubic', 'fk_sucursal', 'compraprov'];

    public function factura()
    {
        return $this->belongsTo(Safact::class, 'numerod', 'NumeroD')
            ->whereColumn('tipofac', 'safact.TipoFac');
    }

    public function producto()
    {
        $comercial = session('comercialid') ;
        return $this->belongsTo(Saprod::class, 'coditem', 'codprod')->where('comercial', $comercial);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }
}
