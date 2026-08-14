<?php
// app/Models/PagoProveedorDetalle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoProveedorDetalle extends Model
{
    protected $table = 'pagos_proveedores_detalles';

    protected $fillable = [
        'pago_id',
        'producto_id',
        'producto_codprod',
        'producto_descrip',
        'cantidad',
        'cantidad_recibida',
        'precio_unitario',
        'subtotal'
    ];

    protected $casts = [
        'cantidad'           => 'integer',
        'cantidad_recibida'  => 'integer',
        'precio_unitario'    => 'decimal:2',
        'subtotal'           => 'decimal:2'
    ];

    public function pago()
    {
        return $this->belongsTo(PagoProveedor::class, 'pago_id');
    }

    public function producto()
    {
        return $this->belongsTo(Saprod::class, 'producto_codprod','codprod')
            ->where('comercial', '=',3);
    }

    // Relación con facturas
    public function facturas()
    {
        return $this->hasMany(FacturaProveedor::class, 'pago_detalle_id');
    }

    public function getPendienteAttribute()
    {
        return $this->cantidad - $this->cantidad_recibida;
    }

    // Obtener cantidad total facturada (suma de todas las facturas)
    public function getTotalFacturadoAttribute()
    {
        return $this->facturas()->sum('cantidad_facturada');
    }

    // Obtener cantidad pendiente por facturar
    public function getPendienteFacturarAttribute()
    {
        return $this->cantidad - $this->total_facturado;
    }

    // Verificar si ya está completamente facturado
    public function getEstaCompletamenteFacturadoAttribute()
    {
        return $this->total_facturado >= $this->cantidad;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($detalle) {
            $detalle->subtotal = $detalle->cantidad * $detalle->precio_unitario;
        });

        static::saved(function ($detalle) {
            $detalle->pago->actualizarEstado();
        });
    }
}
