<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';
    protected $primaryKey = 'id_proveedor';

    protected $fillable = [
        'razon_social',
        'contacto_nombre',
        'contacto_telefono',
        'contacto_email',
        'direccion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function compras()
    {
        return $this->hasMany(Compra::class, 'proveedor_id', 'id_proveedor');
    }
}
