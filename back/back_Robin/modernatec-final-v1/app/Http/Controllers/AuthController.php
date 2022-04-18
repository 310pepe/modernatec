<?php

/**Robin David Rodriguez Bautista
 * 17-03-2022
 * Estudiante adsi
 */


namespace App\Http\Controllers;

use App\Mail\Password_resetMailable;
use Illuminate\Http\Request;
use App\Models\User;    //Modelo usuarios
use Illuminate\Support\Facades\Validator; // validacion para lo que resibe le request
use Illuminate\Support\Facades\Mail;      // instancia para poder utilizar la libreria para enviar correos
use Illuminate\Support\Str;               // libreria para hacer numeros aleatorios
use App\Mail\VerificacionMailable;        // controlador del envio de correos de verificacion
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Optional;
use Illuminate\Validation\Rules\Unique;
use Faker\Generator as Faker;


class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['Registro_Invitado', 'login', 'email_password_reset', 'Validacion_code', 'confirm_reset_password']]); // declaramos las rutas las cuales no es nesesario el token para realisar el registro
    }

    public function Registro_Invitado(Request $request, Faker $faker)
    {
        $user = new User(); // Instanciamos el modelo del usuario para crear el nuevo usuario
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'name' => 'required',
                'apellidos' => 'required|max:50',
                'telefono' => 'required|min:5',
                'email' => 'required|string|email|max:100|unique:users',
                'numero_identificacion' => 'required|min:1',

            ]
        );

        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json([
                'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
            ]);
        } elseif ($validator) // en caso de que se revisva la validacion bien se realizara el siguiente proceso
        {


            $user = User::create(array_merge(  // se le instacia el modelo del usuario y se le pasan los datos resibidos en le request
                $validator->validate(),      // se valida la variable y se crea el nuevo usuario
                ['codes' => $faker->unique()->passthrough(Str::random(20)),],
                // ['password' => bcrypt(123456789)]
            ));
            $code = $user->codes;
            $correo = new VerificacionMailable($code); // se envia instancia del correo que se va a enviar con le parametro de confirmacion de codigo el cual se evalua en el metodo constructor
            Mail::to($user->email)->send($correo);                          // se envia el correo al usuario registrado

            return response()->json([  //cuando se registran los datos se devuelve los siguientes mensajes
                'message' => 'El usuario fue registrado correctamente con los sigientes datos',
                'email' => 'Revise su correo para realizar la confirmacion, se envio al siguiente correo' . $user->email,
                'message1' => 'El usuario que se registro es el siguiente',
                'usuario' => $user
            ]);
        } else {
            return response()->json([ // en caso de que se este generando un erro diferente mostrara el siguente error
                'mensaje' => 'se estan evaluando bien los campos pero se puede estar enviando datos conflictivos para la base de datos porfavor revise que los campos de email, numero de identificacion sean unicos, '
            ]);
        }
    }



    /** funcion para el logueo de un usuario  */

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $request, Faker $faker)
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'email' => 'required',
                'password' => 'required',
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        }

        if (!$token = auth()->attempt(array_merge(  // se pasan los datos registrados en el request
            $validator->validate()      // se validan los datos y se responde con un mensaje
        ))) {
            //en caso de que ocurra algun error en las credenciales se va a enviar el siguiente mensaje
            return response()->json(['error' => 'Las credenciales no son validas'], 401);
        }
        $user = DB::table('users')->select("*")->where('email', $request->email)->get(); //realizamos la consulta eloquent para traer todos los datos del usuario que esta intentando inicar sesion
        if ($user[0]->confirmed == 1) //en caso de que tenga confirmado el correo le mostrar el siguiente condicional
        {
            if ($user[0]->rol <> null && $user[0]->estado === 'true') //en caso de que cumpla con las siguientes condiciones se le devolvera el token que contiene la informacion del usuario
            {
                return $this->respondWithToken($token);
            } else  //en caso de que el usuario este registrado pero no cumpla con las condiciones le aparecera el siguiente mensaje
            {
                return response()->json(['error' => 'El usuario esta registrado pero no tiene acceso a ingresar'], 200);
            }
        } else //en caso de que el usuario no tenga el correo confirmado se llevaran a cabo las siguientes acciones
        {
            DB::table('users')
                /**consulta eloquent para declarar el codigo que va a ser enviado  */
                ->where('id', $user[0]->id)
                ->update(['codes' => $faker->unique()->passthrough(Str::random(20)),]);

            $eloquent = DB::table('users')->select("*")->where('email', $user[0]->email)->get();
            /**traemos todos los datos del usuario al cual le vamos a enviar al correo  */
            $code = $eloquent[0]->codes;
            /**asignamos a la variable code el codigo que se va a enviar */
            $correo = new VerificacionMailable($code); // se envia instancia del correo que se va a enviar con le parametro de confirmacion de codigo el cual se evalua en el metodo constructor
            Mail::to($user[0]->email)->send($correo);
            /**enviamos el correo del  usuario que se esta intentando registrar y devolvemos el siguiente mensaje  */
            return response()->json(['message' => 'Su correo no se encuentra registrado, por favor confirme el correo que se le ha enviado ']);
        }
    }

    /**esta ruta permite ver el perfil de aquellas personas que se hayan logueado  */

    public function me(Request $request)  // esta url trae todos los datos de un usuario como parametro el token
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required'
                /**validamos el token  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        } else {
            return response()->json(auth()->user());   //todos los datos que tiene el token contenido
        }
    }

    public function logout(Request $request) //esta funcion realiza el cerrado de sesion teniendo como parametro el token
    {

        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required'
                /**validamos el token  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        } else {
            auth()->logout();
            /**metodo logout elimina el toke almacenado  */
            /**respondemos con el siguiente mensaje  */
            return response()->json(['message' => 'Se a cerrado la sesión']);
        }
    }

    public function refresh(Request $request) // esta funcion trae un nuevo usuario resibiendo como parametro el token anterior
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'token' => 'required'
                /**validamos el token  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        } else {
            return $this->respondWithToken(auth()->refresh()); //se resibe el token anterior y se crea un token nuevo
        }
    }

    public function email_password_reset(Request $request, $email, Faker $faker) //esta funcion resive como parametro el email al cual se le va a realizar el cambio de contraseña
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'email' => 'required',

                /**validamos que esten llegando todos los datos al request  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        }

        DB::table('users')
            /**realizamos la consulta eloquent para crear un codigo para el cambio de contraseña */
            ->where('email', $email)
            ->update(['codes' => $faker->unique()->passthrough(Str::random(20)),]);

        $eloquent = DB::table('users')->select("*")->where('email', $email)->get(); // traemos los datos de usuario el cual esta intentando reestablecer contraseña
        $code =  $eloquent[0]->codes;
        /**mapeamos el codigo que tiene asignado para enviarlo al correo  */

        $correo = new Password_resetMailable($code);
        /** instanciamos el que queremos enviar y le pasamos como parametro el codigo  */
        Mail::to($email)->send($correo);
        /**enviamos el correo con el codigo  */
        return response()->json([
            /**respondemos con el siguiente mensaje  */
            'message' => "se envio el correo para el cambio de contraseña "
        ]);
    }



    public function Validacion_code(Request $request) //resibimos en el re
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'email' => 'required',
                'codigo' => 'required'
                /**validamos que esten llegando todos los datos al request  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        }

        $email = $request->email;
        /**mapeamos el email que estamos resibiendo en request  */
        $code = $request->codigo;
        /**mapeamos el codigo que estamos resibiendo en request  */
        $user = DB::table('users')->select("*")->where('email', $email)->first();
        /**consulta eloquent para traer los datos del usuario  */

        if ($user->codes === $code)
        /**en caso de que el email y el codigo coinsidan va a devolver el siguiente mensaje  */
        {
            return response()->json([
                'message' => "si puede cambiar contraseña"
            ]);
        } else
        /**en caso de que algun dato este erroneo mostrara el siguiente mensaje  */
        {
            return response()->json([
                "message" => "No conside el codigo para realizar el cambio de contraseña"
            ]);
        }
    }


    public function confirm_reset_password(Request $request)
    /**esta funcion resibe como parametros el email y la password que se va a cambiar  */
    {
        $validator =  Validator::make(
            $request->all(), // la variable validator va a contener todo lo que nos evia por el request
            [
                // validamos los campos que esta resibiendo el request para registrar a un nuevo usuario
                // los campos como los estamos llamando en el request se llaman igual en la base de datos
                'email' => 'required',
                'password' => 'required'
                /**validamos que esten llegando todos los datos al request  */
            ]
        );
        if ($validator->fails()) // en caso de que lo que esta validando el request falla devolvemos el siguente mensaje
        {
            return response()->json(
                [
                    'message' => 'Error al enviar los datos, revise que se esten enviando todos los datos correctamente',
                ]
            );
        }

        $email = $request->email; /**mapeamos el correo que resibimos en el request  */
        $user = DB::table('users')->select("*")->where('email', $email)->first(); /**consulta eloquent para traer todos los datos del usuario al cual le vamos a cambiar la ontraseña   */
        if ($user->codes <> null) /**verificamos que tenga un codigo activo  */
        {
            DB::table('users') /**le actualizamos la contraseña la incriptamos y borramos el codigo  */
                ->where('email', $email)
                ->update(['password' => bcrypt($request->password), 'codes' => null]);
            return response()->json([ /**respondemos con el siguiente mensaje  */
                'message' => "se cambio la contraseña"
            ]);
        } else { /**en caso de que el codigo ya haya expirado o el correo este mal enviaremos el siguiente mensaje  */
            return response()->json([
                "message" => "Esta enviando datos erroneos por favor para cambiar contraseña verifique la informacion"
            ]);
        }
    }





    protected function respondWithToken($token)  // esta funcion crea el token
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60  //esta es la cantidad de caracteres que contiene le token
        ]);
    }
}
