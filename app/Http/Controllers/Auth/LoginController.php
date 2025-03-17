<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\User;


class LoginController extends Controller
{
    public function showLoginForm(){
        return view('auth.login');
    }

    public function login(Request $request)
{
    // Validación de campos vacíos
    $request->validate([
        'usuario' => 'required',
        'password' => 'required',
    ], [
        'usuario.required' => 'Usuario requerido.',
        'password.required' => 'Contraseña requerida.',
    ]);

    // Buscar usuario por nombre de usuario
    $user = User::where('usuario', $request->usuario)->first();

    if (!$user) {
        return back()
            ->withErrors(['usuario' => 'Usuario no encontrado.'])
            ->withInput($request->only('usuario'));
    }

    // Verificar contraseña y condición del usuario
    if (!Auth::attempt(['usuario' => $request->usuario, 'password' => $request->password, 'condicion' => 1])) {
        return back()
            ->withErrors(['password' => 'Contraseña incorrecta, intente de nuevo o comuníquese con el administrador.'])
            ->withInput($request->only('usuario'));
    }

    // Si todo está bien, iniciar sesión y guardar datos en sesión
    $user = Auth::user();
    $persona = $user->persona()->first();
    $request->session()->put('fotografia', $persona->fotografia ?: 'defecto.jpg');
    $request->session()->put('id', $persona->id);

    return redirect()->route('main');
}


    protected function validateLogin(Request $request){
        $this->validate($request,[
            'usuario' => 'required|string',
            'password' => 'required|string'
        ]);

    }

    public function logout(Request $request){
        Auth::logout();
        $request->session()->invalidate();
        return redirect('/');
    }
}
