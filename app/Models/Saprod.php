<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Saprod extends Model
{
    use HasFactory;

    protected $table    = 'saprod';
    protected $fillable = ['codprod','descrip','descrip2','descrip3', 'fijo',
        'marca','refere','codinst','observaciones','activo','clase','cilindraje',
        'esexento','exdecimal','preciodolarfijo', 'cantxempaq','volumen','peso','unidad',
        'preciod','preciod2','costod','costod2','costod3',
        'grupo', 'grupo_nombre', 'grupo_destacado',
        'color_hex', 'color_nombre'
    ];

    public function instancia(){
        $comercial = session('comercialid') ;
        if(!$comercial)$comercial=1;

        return $this->belongsTo(Sainsta::class, 'codinst', 'codinst')
            ->where('comercial',$comercial);
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
            return asset('uploads/productos/' . $this->imagen);
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

    public function variantesDelGrupo()
    {
        if (empty($this->grupo)) {
            return collect([$this]);
        }

        return Saprod::where('comercial', $this->comercial)
            ->where('activo', 1)
            ->where('grupo', $this->grupo)
            ->orderBy('id')
            ->get();
    }

    /**
     * Scope: productos "representantes" del catálogo.
     * Si un producto tiene grupo, solo el de menor `id` del grupo aparece.
     * Los que no tienen grupo, aparecen siempre.
     */
    public function scopeCatalogo($query)
    {
        return $query->where(function ($q) {
            // Sin grupo → siempre visible
            $q->whereNull('grupo')
                ->orWhere('grupo', '')
                // Con grupo → solo el representante (MIN id)
                ->orWhereIn('saprod.id', function ($sub) {
                    $sub->selectRaw('MIN(s2.id)')
                        ->from('saprod as s2')
                        ->whereNotNull('s2.grupo')
                        ->where('s2.grupo', '<>', '')
                        ->groupBy('s2.grupo');
                });
        });
    }

    /**
     * Scope para obtener los "grupos" con al menos un producto visible.
     */
    public function scopeVariantesDeGrupo($query, string $grupo)
    {
        return $query->where('grupo', $grupo);
    }

    /**
     * Nombre a mostrar en el catálogo para un representante.
     */
    public function getNombreCatalogoAttribute()
    {
        return $this->grupo_nombre ?: $this->descrip;
    }

    public function scopeUnicosPorGrupo($query, int $comercial = 1)
    {
        return $query->whereIn('id', function ($sub) use ($comercial) {
            $sub->selectRaw('MIN(s2.id)')
                ->from('saprod as s2')
                ->where('s2.comercial', $comercial)
                ->where('s2.activo', 1)
                ->groupBy('s2.grupo');
        });
    }
}
