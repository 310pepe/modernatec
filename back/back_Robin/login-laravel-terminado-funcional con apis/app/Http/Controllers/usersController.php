<?php

namespace App\Http\Controllers;

use App\Models\contrato;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class usersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */


    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);// declaramos las rutas las cuales no es nesesario el token para realisar el registro
    }

    public function index()
    {

        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});

        if ($rol_map == 1 || $rol_map == 2) {
            $usuarios = User::all();
            return $usuarios;
        }
        return "no tiene acceso para ver los usuarios";
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});

        if ($rol_map == 1) {
            $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como invitado
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

            if($validator->fails()){                        // en caso de algun error en la informacion el dara el siguente error
                return response()->json($validator->errors()->toJson(),400);
            }

            $user = User::create(array_merge(     // incriptacion de la contraseña
                $validator->validate(),
                ['password' => bcrypt($request->password)]
            ));


            return response()->json([             //mensaje de que un usuario se registro exitosamente
                'message' => '¡Usuario registrado exitosamente!',
                'user' => $user
            ], 201);

        }else{
            return "no tiene acceso para registrar nuevas personas";
        }

    }



    public function contrato(Request $request){
        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});

        if ($rol_map == 1) {

            $contrato = new contrato();

            $contrato -> fecha_inicio =  $request -> fecha_inicio;
            $contrato -> fecha_fin =  $request -> fecha_fin;
            $contrato -> id_contrato = $request -> id_contrato;

            $contrato->save();
            return response()->json([             //mensaje de que un usuario se registro exitosamente
                'message' => 'contrato registrado este es el id para asiganar a un usuario',
                'id_contrato' => $contrato->id
            ], 201);

        }else{
            return "el usuario no tiene acceso a este formulario";
        }

     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */


    public function show($id)
    {
        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});

        if ($rol_map == 1 || $rol_map == 2 ) {
        $user = User::findOrfail($id);
        return $user;
        }

        return "este rol no tiene acceso para ver el detalle de los usuarios";
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {

        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});

        if ($rol_map == 1 ) {
                $user_update = User::findOrfail($request->id);
                $user_update -> numero_identificacion =  $request -> numero_identificacion;
                $user_update -> name = $request -> name;
                $user_update -> apellidos =  $request -> apellidos;
                $user_update -> telefono =  $request -> telefono;
                $user_update -> estado =  $request -> estado;
                $user_update -> email =  $request -> email;
                $user_update -> id_area =  $request -> id_area;
                $user_update -> id_contrato =  $request -> id_contrato;
                $user_update -> rol =  $request -> rol;
                $user_update->save();
                return $user_update;
        }
        return "el usuario no puede editar estos datos";

    }




    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }



    public function user_register(Request $request) // esta funcion registra invitados
    {

        $user = response()->json(auth()->user());
        $rol_map = ($user->{'original'}->{'rol'});


        if ($rol_map == 1) {
            $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como invitado
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

            if($validator->fails()){                        // en caso de algun error en la informacion el dara el siguente error
                return response()->json($validator->errors()->toJson(),400);
            }

            $user = User::create(array_merge(     // incriptacion de la contraseña
                $validator->validate(),
                ['password' => bcrypt($request->password)]
            ));

            return response()->json([             //mensaje de que un usuario se registro exitosamente
                'message' => '¡Usuario registrado exitosamente!',
                'user' => $user
            ], 201);

        }else{
            return "no tiene acceso para registrar nuevas personas";
        }


     /*
        $validator = Validator::make($request->all(), [  //valida los campos a ser registrados como invitado
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
        if($validator->fails()){                        // en caso de algun error en la informacion el dara el siguente error
            return response()->json($validator->errors()->toJson(),400);
        }

        $user = User::create(array_merge(     // incriptacion de la contraseña
            $validator->validate(),
            ['password' => bcrypt($request->password)]

        ));

        return response()->json([             //mensaje de que un usuario se registro exitosamente
            'message' => '¡Usuario registrado exitosamente!',
            'user' => $user
        ], 201);*/
    }



}