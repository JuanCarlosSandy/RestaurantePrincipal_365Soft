<?php
 
namespace App;
 
use Illuminate\Database\Eloquent\Model;
 
class DetalleVenta extends Model
{
    protected $table = 'detalle_ventas';
    protected $fillable = [
        'idventa', 
        'codigoComida',
        'cantidad',
        'precio',
        'descuento'
    ];
    public $timestamps = false;
}