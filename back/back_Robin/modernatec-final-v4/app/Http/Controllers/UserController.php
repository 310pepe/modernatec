<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\contrato;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\Registro_User_Mailable;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api'); // declaramos las rutas las cuales no es nesesario el token para realisar el registro
    }

    public function index(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                /**validamos el token */
            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        }
        /** asignamos a la variable $user toda la informacion del token  */
        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que se nos esta entrgando con el token  */

        if ($rol_map == 1 || $rol_map == 2)
        /**en caso de que tenga los siguientes roles asignados va a delvolver la siguiente consulta eloquent */
        {
            $usuarios = User::all();
            /**consulta eloquent que devuleve todos los usuarios  */
            return $usuarios;
            /**muestra los usuarios  */
        }
        return response()->json([
            /**en caso de no tener el rol asignado devolvera el siguiente mensaje  */
            "message" => 'no tiene acceso para ver los usuarios'
        ]);
    }

    public function store(Request $request)
    {
        $user = response()->json(auth()->user());
        /** traemos el token  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que trae el token  */

        if ($rol_map == 1)
        /**si el token trae a un usuario con el rol 1 va a validar los campos para hacer el registro de un nuevo usuario */
        {
            $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como usuario de la empresa
                'token' => 'required',
                'name' => 'required',
                'apellidos' => 'required',
                'numero_identificacion' => 'required',
                'telefono' => 'required',
                'url_imagen',
                'estado' => 'required',
                'email' => 'required|string|email|max:100|unique:users',
                'password' => 'required|string|min:8',
                'id_area' => 'required',
                'id_contrato' => 'required',
                'rol' => 'required',
            ]);

            if ($validator->fails()) {                        // en caso de algun error en la informacion el dara el siguente error
                return response()->json($validator->errors()->toJson(), 400);
            }

            $user = User::create(array_merge(     // incriptacion de la contraseña
                $validator->validate(),
                ['password' => bcrypt($request->password)]
            ));

            /**
             * 23-03-2022
             * Robin David Rodriguez Bautista
             * solicitud de retornar un correo cuando se registra un usuario en el aplicativo
             */
            $code = $request->password;/**pasamos la contraseña a la vista  */
            $correo = new Registro_User_Mailable($code);/**enviamos el correo */
            Mail::to($request->email)->send($correo);/**enviamos el correo que se esta registrando  */
            return response()->json([             //mensaje de que un usuario se registro exitosamente
                'message' => '¡Usuario registrado exitosamente!',
                'user' => $user
            ], 201);
        } else
        /**en caso de que no tenga el rol 1 se mostrara el siguiente mensaje  */
        {
            return response()->json([
                "message" => "no tiene acceso para registrar nuevas personas"
            ]);
        }
    }

    public function contrato(Request $request)
    {
        $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como usuario de la empresa
            'token' => 'required',
            'fecha_inicio' => 'required',
            'fecha_fin',
            'id_contrato' => 'required'

        ]);

        if ($validator->fails()) {                        // en caso de algun error en la informacion el dara el siguente error
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = response()->json(auth()->user());
        /**colocamos los datos del token en una variable  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que nos envia el token  */

        if ($rol_map == 1)
        /**en caso de que el rol que trae el token sea  1 entonces prosegimo a realziar la siguiente consulta eloquent  */
        {

            $contrato = contrato::create(array_merge(  // se le instancia el modelo contrato y se le asignan los campos del request al modelo y se crea un nuevo contrato
                $validator->validate(),      // se valida la variable y se crea el nuevo usuario
            ));

            return response()->json([             //cuando se registro el usuario se muestra el siguiente mensaje
                'message' => 'contrato registrado este es el id para asiganar a un usuario',
                'id_contrato' => $contrato,
            ], 201);
        } else
        /**en caso de que un usuario no este registrado con el rol se le enviara el siguiente mensaje  */
        {
            return "el usuario no tiene acceso a este formulario";
        }
    }

    public function show(Request $request)
    {
        $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como usuario de la empresa
            'token' => 'required',
            'numero_identificacion' => 'required',

        ]);

        if ($validator->fails()) {                        // en caso de algun error en la informacion el dara el siguente error
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = response()->json(auth()->user());
        /**asignamos los datos del token a una variable  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol que nos esta trayendo el token  */

        if ($rol_map == 1 || $rol_map == 2) //en caso de que tengan estos roles se realizara la siguiente consulta eloquent
        {
            $user = DB::table('users')->where('numero_identificacion', $request->numero_identificacion)->first();
            /**consulta eloquent para traer los datos del usuario espesifico  */

            return $user;
            /**retornamos el usuario que se desea observar  */
        }

        return response()->json([
            "message" => "este rol no tiene acceso para ver el detalle de los usuarios"
        ]);
    }


    public function update(Request $request)
    /**resibimos los datos del usuario */
    {
        $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como usuario de la empresa
            'token' => 'required',
        ]);

        if ($validator->fails()) {                        // en caso de algun error en la informacion el dara el siguente error
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = response()->json(auth()->user());
        /** asignamos los datos del token a una variable  */
        $rol_map = ($user->{'original'}->{'rol'});
        /**mapeamos el rol con el que se esta ingresando  */

        if ($rol_map == 1)
        /**en caso de que el usuario tenga el rol uno realizara el siguiente updates   */
        {
            $user_update = User::findOrfail($request->id);
            /**datos que se pueden actualizar  */
            $user_update->numero_identificacion =  $request->numero_identificacion;
            $user_update->name = $request->name;
            $user_update->apellidos =  $request->apellidos;
            $user_update->telefono =  $request->telefono;
            $user_update->estado =  $request->estado;
            $user_update->email =  $request->email;
            $user_update->id_area =  $request->id_area;
            $user_update->id_contrato =  $request->id_contrato;
            $user_update->rol =  $request->rol;
            $user_update->save();
            return $user_update;
        }
        return response()->json([
            /**en caso de que no tenga el acceso se le muestra el siguiente mensaje  */
            "message" => "el usuario no puede editar estos datos"
        ]);
    }
    /**
     * 23-03-2022
     * Robin David Rodriguez Bautista
     * creacion de rutas para la vizualisacion de un contrato
     */
    public function contrato_show(Request $request)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required',
                'id' => 'required'
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
            $contrato = DB::table('contratos')->where('id', $request->id)->first();
            /**consulta para traer el contrato con el id asignado  */
            return response()->json([
                "Contrato" => $contrato
            ]);
        }
    }
}
