<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saopei extends Model
{
    use HasFactory;
    protected $table    = 'saopei';

    protected $fillable = [ 'nrounico',  'tipoopi',  'NumeroD', 'FechaT', 'FechaE', 'FechaV',  'CodEsta', 'CodUsua',
       'CodUbic', 'CodUbic2', 'Signo',  'OTipo', 'ONumero', 'Autori', 'Respon', 'UsoMat', 'CodOper', 'UsoInterno',
       'Notas1', 'Notas2', 'Notas3', 'fk_sucursal', 'descargar'];


    public function sucursal  (){
        return $this->belongsTo(Sasucursal::class, 'fk_sucursal', 'id');
    }

    public function items     (){
        return $this->hasMany(Saitemopi::class, 'NumeroD', 'NumeroD')->whereTipofac('saopei.tipoopi');
    }
}
