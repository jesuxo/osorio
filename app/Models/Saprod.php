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


    public function existencias()
    {
        return $this->hasMany(NewSaexis::class, 'codprod', 'codprod');
    }

    /**
     * Relación con existencias que tienen stock > 0
     * Útil para mostrar solo sucursales con disponibilidad
     */
    public function existenciasConStock()
    {
        return $this->hasMany(NewSaexis::class, 'codprod', 'codprod')
            ->where('existen', '>', 0);
    }

    /**
     * Accessor para obtener el total de existencias sumando todas las sucursales
     */
    public function getTotalExistenciasAttribute()
    {
        return $this->existencias()->sum('existen');
    }

    /**
     * Accessor para obtener el precio de venta (usamos costod3 como precio final)
     */
    public function getPrecioVentaAttribute()
    {
        return $this->costod3 ?? $this->preciod ?? 0;
    }

    /**
     * Accessor para obtener la URL de la imagen (placeholder por ahora)
     */
    public function getImagenUrlAttribute()
    {
        // Si tienes un campo para imagen en la BD, úsalo
        if (isset($this->imagen) && $this->imagen) {
            return asset('storage/productos/' . $this->imagen);
        }
        // Placeholder por defecto
        return asset('build/images/noimagen.jpg');
    }

    /**
     * Scope para productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', 1);
    }

    /**
     * Scope para búsqueda por término
     */
    public function scopeBuscar($query, $termino)
    {
        if (empty($termino)) {
            return $query;
        }

        return $query->where(function($q) use ($termino) {
            $q->where('descrip', 'LIKE', "%{$termino}%")
                ->orWhere('codprod', 'LIKE', "%{$termino}%")
                ->orWhere('marca', 'LIKE', "%{$termino}%")
                ->orWhere('refere', 'LIKE', "%{$termino}%");
        });
    }
}
