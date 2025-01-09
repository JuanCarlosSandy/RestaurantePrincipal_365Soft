<?php

namespace App\Http\Controllers;

use App\Caja;
use App\TransaccionesCaja;
use Illuminate\Http\Request;
use App\ArqueoCaja;
use App\User;
use Carbon\Carbon;

use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;
        $user = \Auth::user();
        
        $query = Caja::join('sucursales', 'cajas.idsucursal', '=', 'sucursales.id')
                    ->join('users', 'cajas.idusuario', '=', 'users.id')
                    ->select(
                        'cajas.id', 
                        'cajas.idsucursal', 
                        'sucursales.nombre as nombre_sucursal',
                        'cajas.idusuario', 
                        'users.usuario as usuario', 
                        'cajas.fechaApertura', 
                        'cajas.fechaCierre', 
                        'cajas.saldoInicial', 
                        'depositos', 
                        'salidas', 
                        'tarifaqrdelivery',
                        'ventas',
                        'ventasContado',
                        'ventasQR',
                        'ventasTarjeta',
                        'compras', 
                        'comprasContado',
                        'saldoFaltante', 
                        'saldoSobrante',
                        'saldoCaja', 
                        'saldototalventas',
                        'estado',
                    );

        if ($user->idrol == 2) {
            $query->where('cajas.idusuario', '=', $user->id);
        }

        if (!empty($buscar)) {
            $query->where('cajas.' . $criterio, 'like', '%' . $buscar . '%');
        }

        $cajas = $query->orderBy('cajas.id', 'desc')->get();

        // Formatear los resultados
        $cajas->transform(function($caja) {
            // Formato de fecha
            $caja->fechaApertura = Carbon::parse($caja->fechaApertura)->format('d/m/Y H:i');
            $caja->fechaCierre = $caja->fechaCierre ? Carbon::parse($caja->fechaCierre)->format('d/m/Y H:i') : 'Caja sin cerrar';

            // Formato de números con punto y coma
            $caja->saldoInicial = number_format($caja->saldoInicial, 2, ',', '.');
            $caja->depositos = number_format($caja->depositos, 2, ',', '.');
            $caja->salidas = number_format($caja->salidas, 2, ',', '.');
            $caja->tarifaqrdelivery = number_format($caja->tarifaqrdelivery, 2, ',', '.');
            $caja->ventas = number_format($caja->ventas, 2, ',', '.');
            $caja->ventasContado = number_format($caja->ventasContado, 2, ',', '.');
            $caja->ventasQR = number_format($caja->ventasQR, 2, ',', '.');
            $caja->ventasTarjeta = number_format($caja->ventasTarjeta, 2, ',', '.');
            $caja->compras = number_format($caja->compras, 2, ',', '.');
            $caja->comprasContado = number_format($caja->comprasContado, 2, ',', '.');
            $caja->saldoFaltante = number_format($caja->saldoFaltante, 2, ',', '.');
            $caja->saldoSobrante = number_format($caja->saldoSobrante, 2, ',', '.');
            $caja->saldoCaja = number_format($caja->saldoCaja, 2, ',', '.');
            $caja->saldototalventas = number_format($caja->saldototalventas, 2, ',', '.');

            return $caja;
        });

        return [
            'cajas' => $cajas
        ];
    }


    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $caja = new Caja();
        $caja->idsucursal = \Auth::user()->idsucursal;
        $caja->idusuario = \Auth::user()->id;
        $caja->fechaApertura = now()->setTimezone('America/La_Paz');
        $caja->saldoInicial = $request->saldoInicial;
        $caja->saldoCaja = $request->saldoInicial;
        $caja->saldototalventas = '0';        
        $caja->estado = '1';
        $caja->save();
    }

    public function depositar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($request->id);
            $caja->depositos = ($request->depositos)+($caja->depositos);
            $caja->saldoCaja += $request->depositos;

            $transacciones = new TransaccionesCaja();
            $transacciones->idcaja = $request->id;
            $transacciones->idusuario = \Auth::user()->id; 
            $transacciones->fecha = now()->setTimezone('America/La_Paz');
            $transacciones->transaccion = $request->transaccion;
            $transacciones->importe = ($request->depositos);

            $transacciones->save();

            $caja->save();
            DB::commit();

            return response()->json('Retiro realizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('Error al realizar el deposito: ' . $e->getMessage(), 500);
        }
        

        
    }

    public function retirar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($request->id);
            $caja->salidas = ($request->salidas) + ($caja->salidas);
            $caja->saldoCaja -= $request->salidas;

            $transacciones = new TransaccionesCaja();
            $transacciones->idcaja = $request->id;
            $transacciones->idusuario = \Auth::user()->id;
            $transacciones->fecha = now()->setTimezone('America/La_Paz');
            $transacciones->transaccion = $request->transaccion;
            $transacciones->importe = $request->salidas;

            // Guardar la transacción primero
            $transacciones->save();

            // Si la transacción se guarda correctamente, guardar los cambios en la caja
            $caja->save();

            DB::commit();

            return response()->json('Retiro realizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('Error al realizar el retiro: ' . $e->getMessage(), 500);
        }
    }

    public function retirarPagoDelivery(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        DB::beginTransaction();

        try {
            $caja = Caja::findOrFail($request->id);
            $caja->salidas = ($request->tarifa_delivery) + ($caja->salidas);
            $caja->saldoCaja -= $request->tarifa_delivery;

            $transacciones = new TransaccionesCaja();
            $transacciones->idcaja = $request->id;
            $transacciones->idusuario = \Auth::user()->id;
            $transacciones->fecha = now()->setTimezone('America/La_Paz');
            $transacciones->transaccion = 'Delivery Pagado';
            $transacciones->importe = $request->tarifa_delivery;

            // Guardar la transacción primero
            $transacciones->save();

            // Si la transacción se guarda correctamente, guardar los cambios en la caja
            $caja->save();

            DB::commit();

            return response()->json('Retiro realizado correctamente', 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json('Error al realizar el retiro: ' . $e->getMessage(), 500);
        }
    }


    public function arqueoCaja(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $arqueoCaja = new ArqueoCaja();
        $arqueoCaja->idcaja = $request->idcaja;
        $arqueoCaja->idusuario = \Auth::user()->id; 
        $arqueoCaja->billete200 = $request->billete200;
        $arqueoCaja->billete100 = $request->billete100;
        $arqueoCaja->billete50 = $request->billete50;
        $arqueoCaja->billete20 = $request->billete20;
        $arqueoCaja->billete10 = $request->billete10;
        $arqueoCaja->moneda5 = $request->moneda5;
        $arqueoCaja->moneda2 = $request->moneda2;
        $arqueoCaja->moneda1 = $request->moneda1;
        $arqueoCaja->moneda050 = $request->moneda050;
        $arqueoCaja->moneda020 = $request->moneda020;
        $arqueoCaja->moneda010 = $request->moneda010;

        $arqueoCaja->save();
    }

    public function cerrar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $caja = Caja::findOrFail($request->id);
        $caja->fechaCierre = now()->setTimezone('America/La_Paz');
        $caja->estado = '0';
        if($request->saldoFaltante >= $caja->saldoCaja){
            $caja->saldoSobrante = ($request->saldoFaltante)-($caja->saldoCaja);
        }else{
            $caja->saldoFaltante = ($request->saldoFaltante)-($caja->saldoCaja);
        }
        $caja->save();
    }
}

