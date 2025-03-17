<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\User;
use App\Persona;
use App\Sucursal;
use App\Exports\UserExport;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Facades\Image;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $buscar = $request->buscar;
        $criterio = $request->criterio;

        if ($buscar == '') {
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idrol', '=', 'roles.id')
                ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
                ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento', 'personas.direccion', 'personas.telefono', 'personas.email', 'personas.fotografia', 'users.usuario', 'users.password', 'users.condicion', 'users.idrol', 'roles.nombre as rol', 'users.idsucursal', 'sucursales.nombre as sucursal')
                ->orderBy('personas.id', 'desc')->get();
        } else {
            $personas = User::join('personas', 'users.id', '=', 'personas.id')
                ->join('roles', 'users.idrol', '=', 'roles.id')
                ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
                ->select('personas.id', 'personas.nombre', 'personas.tipo_documento', 'personas.num_documento', 'personas.direccion', 'personas.telefono', 'personas.email', 'personas.fotografia', 'users.usuario', 'users.password', 'users.condicion', 'users.idrol', 'roles.nombre as rol', 'users.idsucursal', 'sucursales.nombre as sucursal')
                ->where('personas.' . $criterio, 'like', '%' . $buscar . '%')->orderBy('id', 'desc')->get();
        }

        return ['personas' => $personas];
    }

    public function store(Request $request)
    {
        if (!$request->ajax()) return redirect('/');

        try{
            DB::beginTransaction();

            $persona = new Persona();
            $persona->nombre = $request->nombre;
            $persona->tipo_documento = $request->tipo_documento;
            $persona->num_documento = $request->num_documento;
            $persona->direccion = $request->direccion;
            $persona->telefono = $request->telefono;
            $persona->email = $request->email;
            $persona->estadoCli = false;

            
            if($request->hasFile('fotografia'))
            {
                if($request->hasFile('fotografia'))
                {
                    $imagen = $request->file("fotografia");
                    $nombreimagen = Str::slug($request->nombre).".".$imagen->guessExtension();
                    $ruta = public_path("img/usuarios/");

                    // Crear el directorio si no existe
                    if (!File::isDirectory($ruta)) {
                        File::makeDirectory($ruta, 0755, true);
                    }

                    $image = Image::make($imagen);

                    $width = $image->width();
                    $height = $image->height();

                    if ($height > $width) {
                        $image->fit(500, 500);
                    } else {
                        $image->fit(500, 500);
                    }

                    $image->save($ruta . $nombreimagen);
                    // Copiar la imagen al directorio
                    //copy($imagen->getRealPath(), $ruta . $nombreimagen);

                    $persona->fotografia = $nombreimagen;
                }
            }

            $persona->save();

            $user = new User();
            $user->id = $persona->id;
            $user->idrol = $request->idrol;
            $user->idsucursal = $request->idsucursal;
            $user->idpuntoventa = $request->idpuntoventa;
            $user->usuario = $request->usuario;
            $user->password = bcrypt( $request->password);
            $user->condicion = '1';            
            $user->save();

            DB::commit();
        } catch (Exception $e){
            DB::rollBack();
        }
    }

    public function update(Request $request)
{
    if (!$request->ajax()) return redirect('/'); 

    try {
        DB::beginTransaction();

        $user = User::findOrFail($request->id);
        $persona = Persona::findOrFail($user->id);

        // Convertir valores "null" o vacíos en NULL real
        $persona->nombre = trim($request->nombre) !== "null" ? $request->nombre : null;
        $persona->tipo_documento = trim($request->tipo_documento) !== "null" ? $request->tipo_documento : null;
        $persona->num_documento = trim($request->num_documento) !== "null" ? $request->num_documento : null;
        $persona->direccion = trim($request->direccion) !== "null" ? $request->direccion : null;
        $persona->telefono = trim($request->telefono) !== "null" ? $request->telefono : null;
        $persona->email = trim($request->email) !== "null" ? $request->email : null;

        $nombreimagen = "";
        if ($request->hasFile('fotografia')) {
            // Eliminar imagen anterior si existe
            if ($persona->fotografia && Storage::exists('public/img/usuarios/' . $persona->fotografia)) {
                Storage::delete('public/img/usuarios/' . $persona->fotografia);
            }

            $imagen = $request->file("fotografia");
            $nombreimagen = Str::slug($request->nombre) . "." . $imagen->guessExtension();
            $imagen->storeAs('public/img/usuarios', $nombreimagen);

            $ruta = public_path("img/usuarios/");
            $image = Image::make($imagen)->fit(500, 500);
            $image->save($ruta . $nombreimagen);

            $persona->fotografia = $nombreimagen;
        }

        $persona->save();

        $user->usuario = trim($request->usuario) !== "null" ? $request->usuario : null;
        $user->password = !empty($request->password) ? bcrypt($request->password) : $user->password;
        $user->condicion = '1';

        if (!empty($request->idrol)) {
            $user->idrol = $request->idrol;
        }

        if (!empty($request->idsucursal)) {
            $user->idsucursal = $request->idsucursal;
        }

        $user->save();

        DB::commit();
    } catch (Exception $e) {
        DB::rollBack();
    }
}


    public function desactivar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '0';
        $user->save();
    }

    public function activar(Request $request)
    {
        if (!$request->ajax()) return redirect('/');
        $user = User::findOrFail($request->id);
        $user->condicion = '1';
        $user->save();
    }

    public function listarReporteUsuariosExcel()
    {
        return Excel::download(new UserExport, 'usuarios.xlsx');
    }

    public function editarPersona(Request $request)
    {
        if(!$request->ajax()) return redirect('/');

        $persona = User::join('personas','users.id','=','personas.id')
            ->join('roles','users.idrol','=','roles.id')
            ->join('sucursales','users.idsucursal','=','sucursales.id')
            ->select('personas.id','personas.nombre','personas.tipo_documento','personas.num_documento','personas.direccion','personas.telefono','personas.email','personas.fotografia','users.usuario','users.password','users.condicion','users.idrol','roles.nombre as rol', 'users.idsucursal', 'sucursales.nombre as sucursal')
            ->where('personas.id', $request->id);
    
        return ['persona' => $persona->first()];
    }

    public function selectUsuarios(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $filtro = $request->filtro;

        $usuarios = User::join('personas', 'users.id', '=', 'personas.id')
            ->where('personas.nombre', 'like', '%' . $filtro . '%')
            ->select('users.id', 'personas.nombre as nombre')
            ->orderBy('personas.nombre', 'asc')
            ->get();

        return ['usuarios' => $usuarios];
    }

    public function selectUsuariosPorRol(Request $request)
    {
        if (!$request->ajax())
            return redirect('/');

        $filtro = $request->filtro;

        $usuarios = User::join('personas', 'users.id', '=', 'personas.id')
            ->where('users.idrol', '=', $filtro )
            ->select('personas.nombre as nombre','personas.id','users.condicion')
            ->orderBy('personas.nombre', 'asc')
            ->get();

        return ['usuarios' => $usuarios];
    }
}
