<?php
// app/Models/FacturaProveedor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FacturaProveedor extends Model
{
    protected $table = 'facturas_proveedor';

    protected $fillable = [
        'pago_detalle_id',
        'pago_id',
        'numero_factura',
        'fecha_factura',
        'cantidad_facturada',
        'monto_facturado',
        'notas',
        'archivo_path'
    ];

    protected $casts = [
        'fecha_factura' => 'date',
        'cantidad_facturada' => 'integer',
        'monto_facturado' => 'decimal:2'
    ];

    public function pagoDetalle()
    {
        return $this->belongsTo(PagoProveedorDetalle::class, 'pago_detalle_id');
    }

    public function pago()
    {
        return $this->belongsTo(PagoProveedor::class, 'pago_id');
    }

    // Método para calcular el total facturado de un detalle específico
    public static function getTotalFacturadoByDetalle($detalleId)
    {
        return self::where('pago_detalle_id', $detalleId)->sum('cantidad_facturada');
    }
}
