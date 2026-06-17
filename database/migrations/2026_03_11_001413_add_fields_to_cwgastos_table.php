<?php
// database/migrations/[timestamp]_add_cliente_id_to_cwviaje_motos_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cwviajemotos', function (Blueprint $table) {
            // Usamos string porque codclie en saclie es varchar
            $table->string('cliente_codclie')->nullable()->after('viaje_id');
            $table->boolean('facturado')->default(false)->after('precio_por_moto');
            $table->date('fecha_facturacion')->nullable()->after('facturado');

            // No creamos foreign key porque no es bigint, es string
            $table->index('cliente_codclie');
        });
    }

    public function down(): void
    {
        Schema::table('cwviajemotos', function (Blueprint $table) {
            $table->dropColumn(['cliente_codclie', 'facturado', 'fecha_facturacion']);
        });
    }
};
