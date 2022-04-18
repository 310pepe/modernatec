<?php

namespace App\Http\Controllers;

use App\Mail\Citas_UpdateMailable;
use App\Mail\CitasMailable;
use Illuminate\Http\Request;
use App\Models\citas;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\Destroy_Cita_Mailable;
use Illuminate\Support\Facades\DB;

class CitasController extends Controller
{

    public function index(Request $request)
    /**Funcion trae todas las citas creadas */
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
        /**asignamos a una variable la informacion del token  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que trae el token  */

        if ($rol_map == 1)
        /**si el rol es 1 podra vizualisar las citas  una cita  */
        {
            $usuarios = citas::all();
            /**trae todas las citas  */
            return $usuarios;
            /**retorna las citas registradas  */
        }
        return response()->json([
            /**en caso de error se devolvera el siguiente mensaje  */
            "message" => "no tienen acceso para registrar cita"
        ]);
    }

    public function store(Request $request)
    /**esta funcion resive en el request todos los datos para registrar una cita  */
    {
        $user = response()->json(auth()->user());
        /**asignamos a una variable la informacion del token  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que trae el token  */
        if ($rol_map == 1) {
            $cita = new citas();
            /**asignamos los datos del request a la creacion de la cita a la base de datos  */
            $cita->codigo_cita =  $request->codigo_cita;
            $cita->fecha =  $request->fecha;
            $cita->hora =  $request->hora;
            $cita->correo_solicitante =  $request->correo_solicitante;
            $cita->correo_invitado =  $request->correo_invitado;
            $cita->area =  $request->area;
            $cita->numero_identificacion =  $request->numero_identificacion;
            $cita->save();
            $code = $request->codigo_cita;
            $correo = new CitasMailable($code);
            /** instanciamos el que queremos enviar y le pasamos como parametro el codigo  */
            Mail::to($request->correo_solicitante)->send($correo);
            /**enviamos correos a las personas que se les asigno a la cita */
            Mail::to($request->correo_invitado)->send($correo);
            return $cita;
        }

        return response()->json([
            /**en caso de error mostrar el siguiente mensaje  */
            "message" => "este rol no tiene acceso para realziar modificaciones"
        ]);
    }

    public function show(Request $request)
    /**esta resibe como parametro el codigo  */
    {
        $codigo = $request->codigo_cita;
        /**devuelve la cita con la que esta registrado el codigo  */
        $user = DB::table('citas')->select("*")->where('codigo_cita', $codigo)->first();
        return $user;
    }

    public function update(Request $request)
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
        /**asignamos los datos del token a una variable  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que trae el token  */
        if ($rol_map == 1) {/**en caso de que el rol sea uno realiza la siguientes acciones  */

            $cita = citas::findOrfail($request->id);/**resibe el id en el request de id por metodo get  */

            /**resibe los datos los cuales se van a actualizar por metodo post  */
            $cita->codigo_cita =  $request->codigo_cita;
            $cita->fecha =  $request->fecha;
            $cita->hora =  $request->hora;
            $cita->correo_solicitante =  $request->correo_solicitante;
            $cita->correo_invitado =  $request->correo_invitado;
            $cita->area =  $request->area;
            $cita->numero_identificacion =  $request->numero_identificacion;

            $cita->save();/**se guardan los datos  */
            $code = $request->codigo_cita;/**se asigna el codigo a la variable  */
            $correo = new Citas_UpdateMailable($code);/**se envia el codigo a la clase de codigos  */
                     /**enviamos correos a las personas que se les asigno a la cita */
            Mail::to($request->correo_solicitante)->send($correo);
            Mail::to($request->correo_invitado)->send($correo);
            return $cita;
        }

        return response()->json([
            "message" => "este usuario no puede editar citas"
        ]);
    }

    public function destroy(Request $request)/**esta ruta elimina una cita creada  */
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


        $user = response()->json(auth()->user());/**asignamos la informacion del token a una variable  */
        $rol_map = ($user->{'original'}->{'rol'});/**mapeamos el rol con el que biene el token  */
        if ($rol_map == 1)/**si el rol es 1 entonces podra eliminar la cita  */
        {

            /**
             * 23-03-2022
             * Rovin David Rodriguez Bautista
             * se implemento el envio de correos al eliminar una cita
             */
            $cita = DB::table('citas')->where('id', $request->id)->get();
            $code = $cita[0]->codigo_cita;
            $correo = new Destroy_Cita_Mailable($code);/**se envia el codigo a la clase de codigos  */
            /**enviamos correos a las personas que se les asigno a la cita */
             Mail::to($cita[0]->correo_solicitante)->send($correo);
             Mail::to($cita[0]->correo_invitado)->send($correo);
            /**elimina la cita  */
            citas::destroy($request->id);/**eliminamos la cita  */
             return response()->json(/**respondemos con el siguiente mensaje  */
                 [
                     "message"=>"se elimino la cita"
                 ]);
        }
    }
}
