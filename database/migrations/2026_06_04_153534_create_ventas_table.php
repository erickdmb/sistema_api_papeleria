<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id('id_venta');
            $table->date('fecha');
            $table->time('hora');
            
            $table->foreignId('id_cliente')->constrained('clientes', 'id_cliente')->onDelete('restrict');
            $table->foreignId('id_metodo_pago')->constrained('metodos_pago', 'id_metodo_pago')->onDelete('restrict');
            
            $table->decimal('subtotal', 12, 2);
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['COMPLETADA', 'ANULADA'])->default('COMPLETADA');
            $table->timestamps();
            
            $table->index('fecha');
            $table->index('id_cliente');
            $table->index('id_metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};