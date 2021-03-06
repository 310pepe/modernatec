<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CorreosController extends Controller
{
    public function verificacion_correo($code)// como argumento le pasamos a la funcion el codigo el cual vamos a confirmar
    {
        
        $usuario = DB::table('users')->select('*')->where('codes', '=', $code)->first();// realizamos la consulta eloquent para verificar a quien le pertenese el codigo
        
        if($usuario)//si la consulta retorna algo realizamos la siguiente consulta
        {
            DB::table('users')
              ->where('id', $usuario->id)
              ->update(['confirmed' => 1,
                         'codes' => null]);

                         //cuando realizamos la confirmacion colocamos el campo del codigo en null y el campo de confirmacion en 1
                         return view('bienvenida');
        }else{
            return view('correoyaconfirmado');
        }
    }
}
