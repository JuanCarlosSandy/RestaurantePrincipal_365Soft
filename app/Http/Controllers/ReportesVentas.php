<?php

namespace App\Http\Controllers;

use App\DetalleVenta;
use App\Moneda;
use App\Venta;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportesVentas extends Controller
{
    public function ResumenVentasPorDocumento(Request $request) {
        $fechaInicio = $request->fechaInicio . ' 00:00:00';
        $fechaFin = $request->fechaFin . ' 23:59:59';
    
        \Log::info("Fecha de inicio: " . $fechaInicio);
        \Log::info("Fecha de fin: " . $fechaFin);
    
        $ventas = Venta::join('users', 'ventas.idusuario', '=', 'users.id')
            ->join('roles', 'users.idrol', '=', 'roles.id')
            ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
            ->select(
                'ventas.num_comprobante as Factura',
                'ventas.id',
                'ventas.tipoEntrega as Tipo_entrega',
                'ventas.idtipo_pago as Tipo_venta',
                'sucursales.nombre as Nombre_sucursal',
                'ventas.fecha_hora',
                'roles.nombre AS nombre_rol',
                'users.usuario',
                'ventas.cliente AS cliente',
                'ventas.total AS importe_BS'
            )
            ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin])
            ->orderBy('ventas.fecha_hora', 'asc');
    
        if ($request->has('estadoVenta')) {
            $estado_venta = $request->estadoVenta;
            \Log::info("Estado de la venta: " . $estado_venta);
            if ($estado_venta !== 'Todos') {
                $ventas->where('ventas.estado', '=', $estado_venta);
            }
        }
    
        if ($request->has('sucursal') && $request->sucursal !== 'undefined') {
            $sucursal = $request->sucursal;
            \Log::info("Sucursal: " . $sucursal);
            $ventas->where('sucursales.id', $sucursal);
        }
    
        if ($request->has('ejecutivoCuentas') && $request->ejecutivoCuentas !== 'undefined') {
            $ejecutivoCuentas = $request->ejecutivoCuentas;
            \Log::info("Ejecutivo de cuentas: " . $ejecutivoCuentas);
            $ventas->where('ventas.idusuario', $ejecutivoCuentas);
        }
    
        $ventas = $ventas->get();
        
        $total_importeBs = 0;
    
        foreach ($ventas as &$venta) {
            // Formatear fecha y hora
            $venta->fecha_hora = Carbon::parse($venta->fecha_hora)->format('d/m/Y H:i:s');
    
            // Formatear importe_BS
            $venta->importe_BS = number_format($venta->importe_BS, 2, ',', '.');
    
            $total_importeBs += str_replace(['.', ','], ['', '.'], $venta->importe_BS);
    
            // Mapeo para tipo_entrega
            switch ($venta->Tipo_entrega) {
                case 'L':
                    $venta->Tipo_entrega = 'Llevar';
                    break;
                case 'D':
                    $venta->Tipo_entrega = 'Delivery';
                    break;
                default:
                    $venta->Tipo_entrega = 'Mesa';
                    break;
            }
    
            // Mapeo para tipo_venta
            switch ($venta->Tipo_venta) {
                case 1:
                    $venta->Tipo_venta = 'EFECTIVO';
                    break;
                case 7:
                    $venta->Tipo_venta = 'QR';
                    break;
                default:
                    $venta->Tipo_venta = 'OTRO';
                    break;
            }
        }
    
        \Log::info("Ventas encontradas: " . $ventas->count());
    
        return [
            'ventas' => $ventas,
            'total_BS' => number_format($total_importeBs, 2, ',', '.'),
        ];
    }


    public function ResumenVentasPorDocumentoDetallado(Request $request) {
        $fechaInicio = $request->fechaInicio . ' 00:00:00';
        $fechaFin = $request->fechaFin . ' 23:59:59';
    
        $ventas = DetalleVenta::select(
                'detalle_ventas.cantidad',
                'detalle_ventas.precio',
                'detalle_ventas.descuento',
                'detalle_ventas.codigoComida',
                'ventas.num_comprobante as Factura',
                'ventas.id',
                'ventas.total as Total',
                'ventas.fecha_hora as Fecha',
                'users.usuario as Vendedor',
                'roles.nombre as Ejecutivo_de_Venta',
                'sucursales.nombre as Sucursal',
                'ventas.tipoEntrega as Tipo_entrega',
                'ventas.idtipo_pago as Tipo_venta',
                'categoria_menu.nombre as nombre_categoria',
                DB::raw("ROUND((detalle_ventas.precio / detalle_ventas.cantidad), 2) AS precio_unitario"),
                'articulos.nombre as articulo_nombre',
                'menu.nombre as menu_nombre'
            )
            ->join('ventas', 'detalle_ventas.idventa', '=', 'ventas.id')
            ->join('users', 'ventas.idusuario', '=', 'users.id')
            ->join('roles', 'users.idrol', '=', 'roles.id')
            ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
            ->leftJoin('articulos', 'detalle_ventas.codigoComida', '=', 'articulos.codigo')
            ->leftJoin('menu', 'detalle_ventas.codigoComida', '=', 'menu.codigo')
            ->leftJoin('categoria_menu', 'articulos.idcategoria_producto', '=', 'categoria_menu.id')
            ->whereBetween('ventas.fecha_hora', [$fechaInicio, $fechaFin])
            ->orderBy('ventas.fecha_hora');
    
        if ($request->has('estadoVenta')) {
            $estado_venta = $request->estadoVenta;
            if ($estado_venta !== 'Todos') {
                $ventas->where('ventas.estado', '=', $estado_venta);
            }
        }
    
        if ($request->has('sucursal') && $request->sucursal !== 'undefined') {
            $ventas->where('sucursales.id', $request->sucursal);
        }
    
        if ($request->has('ejecutivoCuentas') && $request->ejecutivoCuentas !== 'undefined') {
            $ventas->where('ventas.idusuario', $request->ejecutivoCuentas);
        }
    
        $ventas = $ventas->get();
    
        foreach ($ventas as &$venta) {
            // Formatear la fecha y hora
            $venta->Fecha = Carbon::parse($venta->Fecha)->format('d/m/Y H:i:s');
    
            // Formatear el precio y el total en el formato deseado
            $venta->Total = number_format($venta->Total, 2, ',', '.');
            $venta->precio = number_format($venta->precio, 2, ',', '.');
            $venta->precio_unitario = number_format($venta->precio_unitario, 2, ',', '.');
    
            // Mapeo para tipo_entrega
            switch ($venta->Tipo_entrega) {
                case 'L':
                    $venta->Tipo_entrega = 'Llevar';
                    break;
                case 'D':
                    $venta->Tipo_entrega = 'Delivery';
                    break;
                default:
                    $venta->Tipo_entrega = 'Mesa';
                    break;
            }
    
            // Mapeo para tipo_venta
            switch ($venta->Tipo_venta) {
                case 1:
                    $venta->Tipo_venta = 'EFECTIVO';
                    break;
                case 7:
                    $venta->Tipo_venta = 'QR';
                    break;
                default:
                    $venta->Tipo_venta = 'OTRO';
                    break;
            }
    
            // Verificar si el código pertenece a artículos o menú y asignar los valores correspondientes
            if ($venta->articulo_nombre) {
                $venta->nombre = $venta->articulo_nombre;
            } elseif ($venta->menu_nombre) {
                $venta->nombre = $venta->menu_nombre;
            } else {
                $venta->nombre = 'Nombre no disponible';
            }
    
            // Eliminar los campos no necesarios después de la asignación
            unset($venta->articulo_nombre);
            unset($venta->menu_nombre);
        }
    
        return [
            'ventas' => $ventas,
        ];
    }
    
    
    
    

    public function ventasPorProducto(Request $request){
        $fechaInicio = $request->fechaInicio;
        $fechaFin = $request->fechaFin;
        $fechaInicio = $fechaInicio . ' 00:00:00';
        $fechaFin = $fechaFin . ' 23:59:59';
        $ventas = Venta::join('detalle_ventas','ventas.id','detalle_ventas.idventa')
        ->join('personas','personas.id','=','ventas.idcliente')
        ->join('articulos','detalle_ventas.idarticulo','=','articulos.id')
        ->join('categorias','articulos.idcategoria','=','categorias.id')
        ->join('marcas','articulos.idmarca','=','marcas.id')
        ->join('industrias','articulos.idindustria','=','industrias.id')
        ->join('medidas','articulos.idmedida','=','medidas.id')
        ->join('users','ventas.idusuario','=','users.id')
        ->join('sucursales','users.idsucursal','=','sucursales.id')
        ->select('ventas.fecha_hora',
                'personas.nombre',
            'detalle_ventas.*',
            'articulos.codigo',
            'articulos.descripcion',
            'categorias.nombre as nombre_categoria',
            'marcas.nombre as nombre_marca',
            'industrias.nombre as nombre_industria',
            'medidas.descripcion_medida as medida')
        ->whereBetween('fecha_hora', [$fechaInicio, $fechaFin]);

        if ($request->has('sucursal') && $request->sucursal !== 'undefined') {
                $sucursal = $request->sucursal;
                $ventas->where('sucursales.id', $sucursal);
            }

        if ($request->has('idcliente') && $request->idcliente !== 'undefined') {
                $cliente = $request->idcliente;
                $ventas->where('ventas.idcliente' , $cliente);
            }
        if ($request->has('articulo') && $request->articulo !== 'undefined') {
                $articulo = $request->articulo;
                $ventas->where('detalle_ventas.idarticulo' , $articulo);
            }
        if ($request->has('marca') && $request->marca !== 'undefined') {
                $idmarca = $request->marca;
                $ventas->where('articulos.idmarca' , $idmarca);
                
            }
        if ($request->has('linea') && $request->linea !== 'undefined') {
                $idlinea = $request->linea;
                $ventas->where('articulos.idcategoria' , $idlinea);
                
            }
        if ($request->has('industria') && $request->industria !== 'undefined') {
                $idindustria = $request->industria;
                $ventas->where('articulos.idindustria' , $idindustria);
                
            }
        $ventas = $ventas->get();
        return ['resultados' =>$ventas];
    }
    
    

}