<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sainsta extends Model
{
    use HasFactory;
    protected $table    = 'sainsta';
    protected $fillable = ['codinst', 'descrip', 'insPadre', 'nivel', 'tipoIns', 'DEsComp', 'codalte', 'desseri'];

    public function padre(){
        return $this->belongsTo(Sainsta::class, 'insPadre', 'codinst') ;
    }

    public function hijos  (){
        return $this->hasMany(Sainsta::class, 'insPadre', 'id') ;
    }

    public function productos  (){
        $comercial = session('comercialid') ;
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')->where('comercial',$comercial);
    }

    public function productosexistencias  (){
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('saprod.existen', '<>', 0)
            ->where('saprod.comercial',1);
    }

    public function newproductosexistencias  (){
        $comercial = session('comercialid') ;
        return $this->hasMany(Saprod::class, 'codinst', 'codinst')
            ->where('saprod.newexisten', '<>', 0)
            ->where('saprod.comercial',$comercial);
    }

    public function servicios  (){
        return $this->hasMany(Saserv::class, 'codinst', 'codinst');
    }

    public function comercial  (){
        return $this->belongsTo(Sacomercial::class, 'comercial', 'id');
    }

    /**
     * Scope para categorías principales (nivel 1)
     */
    public function scopePrincipales($query)
    {
        return $query->where('nivel', 1)
            ->where('insPadre', 0)
            ->where('tipoIns', 0);
    }

    /**
     * Scope para categorías hijas de una categoría padre
     */
    public function scopeHijasDe($query, $codinstPadre)
    {
        return $query->where('insPadre', $codinstPadre);
    }

    /**
     * Scope para categorías de un comercial específico
     */
    public function scopeDelComercial($query, $comercialId)
    {
        return $query->where('comercial', $comercialId);
    }
}
