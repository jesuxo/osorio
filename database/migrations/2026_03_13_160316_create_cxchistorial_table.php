<?php
// database/migrations/[timestamp]_create_cxchistorial_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cxchistorial', function (Blueprint $table) {
            $table->id();
            $table->string('cliente_codclie');
            $table->foreignId('viaje_moto_id')->constrained('cwviajemotos');
            $table->foreignId('viaje_id')->constrained('cwviajes');
            $table->string('modelo_moto');
            $table->integer('cantidad');
            $table->decimal('monto', 10, 2);
            $table->enum('tipo', ['cobro', 'reversion']); // cobro = se facturó, reversion = se anuló
            $table->timestamp('fecha_hora');
            $table->foreignId('usuario_id')->nullable()->constrained('users');
            $table->text('observaciones')->nullable();
            $table->timestamps();

            $table->index(['cliente_codclie', 'fecha_hora']);
            $table->index('viaje_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cxchistorial');
    }
};
