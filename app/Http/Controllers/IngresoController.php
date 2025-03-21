<?php
 
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Caja;
use App\Ingreso;
use App\Articulo;
use App\DetalleIngreso;
use App\User;
use App\Notifications\NotifyAdmin;
use Illuminate\Support\Facades\Log;
 
class IngresoController extends Controller
{
    public function index(Request $request)
{
    if (!$request->ajax()) return redirect('/');

    $buscar = $request->buscar;
    $criterio = $request->criterio;
    $user = \Auth::user();
    $userRole = $user->idrol;

    $query = Ingreso::join('personas', 'ingresos.idproveedor', '=', 'personas.id')
        ->join('users', 'ingresos.idusuario', '=', 'users.id')
        ->select(
            'ingresos.id', 
            'ingresos.tipo_comprobante', 
            'ingresos.serie_comprobante',
            'ingresos.num_comprobante', 
            'ingresos.fecha_hora', 
            'ingresos.impuesto', 
            'ingresos.total',
            'ingresos.estado', 
            'personas.nombre', 
            'users.usuario'
        );

    if ($userRole == 3) {
        $query->where('ingresos.idusuario', $user->id);
    }

    if ($buscar != '') {
        $query->where('ingresos.' . $criterio, 'like', '%' . $buscar . '%');
    }

    $ingresos = $query->orderBy('ingresos.id', 'desc')->paginate(11);

    // Formatear fecha y total en cada ingreso
    $ingresos->transform(function ($ingreso) {
        $ingreso->fecha_hora = Carbon::parse($ingreso->fecha_hora)->format('d/m/Y H:i');
        $ingreso->total = number_format($ingreso->total, 2, ',', '.');
        return $ingreso;
    });

    return [
        'pagination' => [
            'total'        => $ingresos->total(),
            'current_page' => $ingresos->currentPage(),
            'per_page'     => $ingresos->perPage(),
            'last_page'    => $ingresos->lastPage(),
            'from'         => $ingresos->firstItem(),
            'to'           => $ingresos->lastItem(),
        ],
        'ingresos' => $ingresos
    ];
}


    public function obtenerCabecera(Request $request){
        if (!$request->ajax()) return redirect('/');
 
        $id = $request->id;
         

        $ingreso = Ingreso::join('personas','ingresos.idproveedor','=','personas.id')
        ->join('users','ingresos.idusuario','=','users.id')
        ->select('ingresos.id','ingresos.tipo_comprobante','ingresos.serie_comprobante',
        'ingresos.num_comprobante','ingresos.fecha_hora','ingresos.impuesto','ingresos.total',
        'ingresos.estado','personas.nombre','users.usuario')
        ->where('ingresos.id','=',$id)
        ->orderBy('ingresos.id', 'desc')->take(1)->get();

         
        return [
           
            'ingreso' => $ingreso
        ];
    }
    public function obtenerDetalles(Request $request){
        if (!$request->ajax()) return redirect('/');
 
        $id = $request->id;
         
        $detalles = DetalleIngreso::join('articulos','detalle_ingresos.idarticulo','=','articulos.id')
        ->select('detalle_ingresos.cantidad','detalle_ingresos.precio','articulos.nombre as articulo')
        ->where('detalle_ingresos.idingreso','=',$id)
        ->orderBy('detalle_ingresos.id', 'desc')->get();

         
        return [
           
            'detalles' => $detalles
        ];
    }
    public function store(Request $request)
{
    if (!$request->ajax()) return redirect('/');

    try {
        DB::beginTransaction();

        // Eliminar la verificación de la caja abierta
        // $ultimaCaja = Caja::latest()->first();

        // if ($ultimaCaja) {
        //     if ($ultimaCaja->estado == '1') {
        $ingreso = new Ingreso();
        $ingreso->idproveedor = $request->idproveedor;
        $ingreso->idusuario = \Auth::user()->id;
        $ingreso->tipo_comprobante = $request->tipo_comprobante;
        $ingreso->serie_comprobante = $request->serie_comprobante;
        $ingreso->num_comprobante = $request->num_comprobante;
        $ingreso->fecha_hora = now()->setTimezone('America/La_Paz');
        $ingreso->impuesto = $request->impuesto;
        $ingreso->total = $request->total;
        $ingreso->estado = 'Registrado';
        
        // Eliminar la asignación del idcaja
        // $ingreso->idcaja = $ultimaCaja->id;
        $ingreso->save();

        // Eliminar la actualización de la caja
        // $ultimaCaja->comprasContado = ($request->total) + ($ultimaCaja->comprasContado);
        // $ultimaCaja->save();

        $detalles = $request->data;

        //Recorro todos los elementos
        Log::info('PRODUCTOS:', [
            'DATA' => $detalles,
        ]);
        foreach ($detalles as $ep => $det) {
            $detalle = new DetalleIngreso();
            $detalle->idingreso = $ingreso->id;
            $detalle->idarticulo = $det['idarticulo'];
            $detalle->cantidad = $det['cantidad'];
            $detalle->precio = $det['precio'];
            $detalle->save();
        }

        $fechaActual = date('Y-m-d');
        $numVentas = DB::table('ventas')->whereDate('created_at', $fechaActual)->count();
        $numIngresos = DB::table('ingresos')->whereDate('created_at', $fechaActual)->count();

        $arregloDatos = [
            'ventas' => [
                'numero' => $numVentas,
                'msj' => 'Ventas'
            ],
            'ingresos' => [
                'numero' => $numIngresos,
                'msj' => 'Ingresos'
            ]
        ];
        $allUsers = User::all();

        foreach ($allUsers as $notificar) {
            User::findOrFail($notificar->id)->notify(new NotifyAdmin($arregloDatos));
        }

        DB::commit();
        return [
            'id' => $ingreso->id
        ];

        // } else {
        //     return [
        //         'id' => -1,
        //         'caja_validado' => 'Debe tener una caja abierta'
        //     ];
        // }
        // } else {
        //     return [
        //         'id' => -1,
        //         'caja_validado' => 'Debe crear primero una apertura de caja'
        //     ];
        // }
    } catch (Exception $e) {
        DB::rollBack();
    }
}

 
    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $ingreso = Ingreso::findOrFail($request->id);
        $ingreso->estado = 'Anulado';
        $ingreso->save();
    }
}