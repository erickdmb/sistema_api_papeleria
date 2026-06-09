<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compras', function (Blueprint $table) {
            $table->id('id_compra');
            $table->date('fecha');
            
            $table->foreignId('proveedor_id')->constrained('proveedores', 'id_proveedor')->onDelete('restrict');
            
            $table->string('ruc', 50)->nullable();
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['REGISTRADA', 'ANULADA'])->default('REGISTRADA');
            $table->timestamps();
            
            $table->index('fecha');
            $table->index('proveedor_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};