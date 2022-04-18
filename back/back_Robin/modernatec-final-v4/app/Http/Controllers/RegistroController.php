<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\registro;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RegistrosExport;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;

class RegistroController extends Controller
{
    public function Registro_entrada(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                'hora_ingreso' => 'required',
                'numero_identificacion' => 'required',

                /**validamos el token  */
            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        }


        /**asignamos los datos del token a una variable  */
        $user = response()->json(auth()->user());

        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos con el rol que va a ingresar  */
        if ($rol_map == 3) {
            $entrada = new registro();

            $entrada->fecha = $request->fecha;
            /**resibimos en el request la fecha  */
            $entrada->hora_ingreso = $request->hora_ingreso;
            /**resibimos en el request la hora */
            $entrada->numero_identificacion = $request->numero_identificacion;
            /**resibimos el id del usuario que se esta registrando  */
            $entrada->save();
            /**guardamos el registro  */
            return response()->json([
                "message" => "se registro la entrada"
            ]);
        } else {
            return response()->json([
                "message" => "el usuario no es de seguridad no pude registrar la entrada "
            ]);
        }
    }

    public function Registro_salida(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                'numero_identificacion' => 'required',
                'hora_salida' => 'required',
                /**validamos el token  */
            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        }



        /**asignamos los datos del token a una variable  */
        $user = response()->json(auth()->user());

        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos con el rol que va a ingresar  */
        if ($rol_map == 3) {

            $id = $request->numero_identificacion;
            /**instanciamos el id al cual se le va a registrar la salida  */

            $user = DB::table('registros')->orderBy('id', 'desc')->select("*")->where('numero_identificacion', $id)->first();
            /**consulta eloquent para filtrar le registro al cual se le va a registrar la salida  */


            if ($user->hora_salida == null) {
                DB::table('registros')
                    /**consulta eloquent para registrar la salida   */
                    ->where('id', $user->id)
                    ->update(['hora_salida' => $request->hora_salida,]);

                /**en caso de que todo este bien retornara este mensaje  */
                return response()->json([
                    "messaje" => "salida registrada"
                ]);
            } else {
                /**mensaje en caso de que el usuario ya halla registrado una salida */
                return response()->json([
                    "message" => "el usuario ya registro la salida "
                ]);
            }
        }
    }


    public function Base_datos(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                /**validamos el token  */
            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        }
        $user = response()->json(auth()->user());
        /**asignamos la informacion del token a una variable  */

        $rol_map = ($user->{'original'}->{'rol'});
        /**asignamos el rol que nos trae el token  */
        /**mapeamos con el rol que va a ingresar  */
        if ($rol_map == 1)
        /**solo el rol 1 (registros) puede crear la base de datos */
        {


            $numero_identificacion = DB::table('users')->select("numero_identificacion")->get();
            /**consulta eloquent para traer todos los numero de identificacion  */
            $email = DB::table('users')->select("email")->get();
            /** consulta eloquent que trae todos los correos de los usuario */
            $array_identificacion = array();
            /**declaramos con un array todos los numeros de identificacion de los usuarios */
            foreach ($numero_identificacion as $key => $value)
            /**recorremos todos los numeros de identificacion */
            {
                array_push($array_identificacion, $value->numero_identificacion);
            }
            $array_email = array();
            /**asignamos todos los emails a este array  */
            foreach ($email as $key => $value) {
                /**recorremos todos los emails  */
                array_push($array_email, $value->email);
            }
            $file = $request->file('file');
            /**resivimos el archivo y se lo asignamos a una variable  */

            Excel::import($user = new UsersImport($array_identificacion, $array_email), $file);
            /**instanciamos a la clase UsersImport y con la libreria de excel le asigamos el valor y le pasamos los datos al metodo constructor  */
            return response()->json([
                /**respondemos con los siguientes mensajes  */
                "message" => "se cargo exitosamente",
                "message1" => "los usuario que no se cargaron fueron los siguientes ",
                "errores" => $user->error,
            ]);
        }
    }

    public function export(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                /**validamos el token  */
            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        }
        $user = response()->json(auth()->user());
        /**asignamos la informacion del token a una variable  */

        $rol_map = ($user->{'original'}->{'rol'});
        /**asignamos el rol que nos trae el token  */
        /**mapeamos con el rol que va a ingresar  */
        if ($rol_map == 1)
        /**solo el rol 1 (registros) puede descargar  los registros de los usuarios que entraron */
        {

            $fecha_1 = $request->fecha_1;
            /**fecha 1 para el filtrado  */
            $fecha_2 = $request->fecha_2;
            /**fecha dos del filtrado  */
            return (new RegistrosExport($fecha_1, $fecha_2))->download('registro.xlsx');
            /**devolvemos la descarga de un archivo xlsx  */
            // return (new RegistrosExport($fecha_1,$fecha_2))->download('archivo-list.xlsx');
        }
    }




}
