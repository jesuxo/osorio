<?php
// database/migrations/[timestamp]_add_campos_proveedor_to_cwviaje_motos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cwviajemotos', function (Blueprint $table) {
            // Indicadores
            $table->boolean('proveedor_paga')->default(false)->after('facturado');
            $table->string('proveedor_codprov')->nullable()->after('proveedor_paga');

            // Montos del proveedor
            $table->decimal('monto_transporte_proveedor', 10, 2)->nullable()->after('proveedor_codprov');
            $table->decimal('retencion_proveedor', 10, 2)->nullable()->after('monto_transporte_proveedor');
            $table->decimal('descuento_aplicado_cliente', 10, 2)->nullable()->after('retencion_proveedor');

            // Cálculos
            $table->decimal('monto_esperado_cliente', 10, 2)->nullable()->after('descuento_aplicado_cliente');
            $table->decimal('monto_real_cliente', 10, 2)->nullable()->after('monto_esperado_cliente');
            $table->decimal('diferencia', 10, 2)->nullable()->after('monto_real_cliente');

            // Estado de conciliación
            $table->enum('estado_conciliacion', ['pendiente', 'conciliado', 'discrepancia'])->default('pendiente')->after('diferencia');
            $table->text('notas_conciliacion')->nullable()->after('estado_conciliacion');
            $table->timestamp('fecha_conciliacion')->nullable()->after('notas_conciliacion');
            $table->foreignId('conciliado_por')->nullable()->constrained('users')->after('fecha_conciliacion');
        });
    }

    public function down(): void
    {
        Schema::table('cwviajemotos', function (Blueprint $table) {
            $table->dropColumn([
                'proveedor_paga',
                'proveedor_codprov',
                'monto_transporte_proveedor',
                'retencion_proveedor',
                'descuento_aplicado_cliente',
                'monto_esperado_cliente',
                'monto_real_cliente',
                'diferencia',
                'estado_conciliacion',
                'notas_conciliacion',
                'fecha_conciliacion',
                'conciliado_por'
            ]);
        });
    }
};
