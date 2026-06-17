<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprod extends Model
{
    use HasFactory;

    protected $table    = 'saprod';
    protected $fillable = ['codprod','descrip','descrip2','descrip3', 'fijo',
                          'marca','refere','codinst','observaciones','activo',
                          'esexento','exdecimal','preciodolarfijo', 'cantxempaq','volumen','peso','unidad',
                          'preciod','preciod2','costod','costod2','costod3',
                        ];

    public function instancia(){
        $comercial = session('comercialid') ;
        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')->where('comercial',$comercial);
    }

    public function sucursales  (){
        return $this->hasMany(Saprodsucursal::class, 'codprod', 'codprod');
    }

    public function instanciatres(){

        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')
            ->where('comercial', '=', 1);
    }

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }
}
