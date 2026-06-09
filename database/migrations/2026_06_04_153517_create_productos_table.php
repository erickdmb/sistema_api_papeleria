<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->id('id_producto');
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            
            // Relación explícita con la llave primaria personalizada
            $table->foreignId('categoria_id')->constrained('categorias', 'id_categoria')->onDelete('cascade');
            
            $table->decimal('precio_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->string('sku', 50)->unique();
            $table->string('marca', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('tamaño', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
            
            $table->index('nombre');
            $table->index('stock_actual');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};