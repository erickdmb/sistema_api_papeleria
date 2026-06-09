<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';
    protected $primaryKey = 'id_producto';

    protected $fillable = [
        'nombre',
        'descripcion',
        'categoria_id',
        'precio_compra',
        'precio_venta',
        'stock_actual',
        'stock_minimo',
        'sku',
        'marca',
        'color',
        'tamaño',
        'activo',
    ];

    protected $casts = [
        'precio_compra' => 'decimal:2',
        'precio_venta' => 'decimal:2',
        'stock_actual' => 'integer',
        'stock_minimo' => 'integer',
        'activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'categoria_id', 'id_categoria');
    }

    public function ventaDetalles()
    {
        return $this->hasMany(VentaDetalle::class, 'id_producto', 'id_producto');
    }

    public function compraDetalles()
    {
        return $this->hasMany(CompraDetalle::class, 'id_producto', 'id_producto');
    }
}
