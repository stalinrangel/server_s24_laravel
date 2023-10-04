<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
//use Illuminate\Support\Facades\DB;
use Hash;
use DB;
use Mail;
use Session;
use Redirect;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UsuarioController extends Controller
{

    public function ciudad($ciudad_id)
    {
        $ciudad = \App\Ciudad::with('zonas')->get();
        $zonas=[];

        for ($i=0; $i < count($ciudad); $i++) { 
            if ($ciudad[$i]->id==$ciudad_id) {
                for ($j=0; $j < count($ciudad[$i]->zonas); $j++) { 
                    array_push($zonas,$ciudad[$i]->zonas[$j]->id);
                }
            }
        }
        return $zonas;
    }


    public function index_panel(Request $request)
    {
        
        $tipo=[0,1,5,6,7];
        $usuarios = \App\User::select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'intentos', 'confirmado','imagen', 'tipo_usuario', 'zona_id','token_notificacion', 'status','created_at')
            ->whereIn('tipo_usuario', $tipo)->
            where('ciudad',$request->input('ciudad_id'))->get();
        //return response()->json(['usuarios'=>$usuarios], 200);
        


       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json(['usuarios'=>$usuarios], 200);
        } 
    }

    public function cerrar_sesion($id)
    {
        $usuario=\App\User::find($id);

        $usuario->token_notificacion = "";


        $repartidores = \App\Repartidor::where('usuario_id',$id)->first();
        if ($repartidores->activo==3||$repartidores->activo==4) {
            
        }else{
            $repartidores->activo = 2;
        }
        
        return response()->json(['message'=>'Usuario actualizado con éxito.', 'usuario'=>$usuario], 200);
        if ($usuario->save()) {
                $repartidores->save();
                return response()->json(['message'=>'Usuario actualizado con éxito.', 'usuario'=>$usuario], 200);
        }else{
                return response()->json(['error'=>'Error al actualizar el usuario.'], 500);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los usuarios clientes
       // return response()->json(['zonas'=>$zonas], 200);
        $usuarios = \App\User::select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'zona_id','token_notificacion', 'status','created_at')
            ->with(['chat_cliente' => function ($query) {
                $query->select('id', 'admin_id', 'usuario_id');
            }])->with('zonas')
            ->where('tipo_usuario', 2)->
            whereIn('zona_id',$zonas)->get();
        //return response()->json(['usuarios'=>$usuarios], 200);
        for ($i=0; $i < count($usuarios); $i++) { 
            $curso = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',4)->get();

            $usuarios[$i]->encurso=count($curso);
            $usuarios[$i]->enfinalizados=count($final);


            $calificaciones = \App\Calificacion::where('califique_a',$usuarios[$i]->id)->get();
           
            
            if (count($calificaciones)!=0)
            {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio=$promedio+$calificaciones[$j]->puntaje;
                }
                $promedio=$promedio/count($calificaciones);
                $usuarios[$i]->promedio=$promedio;
            }else{
                $usuarios[$i]->promedio=0;
            } 
            
        }


       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json(['usuarios'=>$usuarios], 200);
        } 
    }

    public function index_zonas(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los usuarios clientes
       // return response()->json(['zonas'=>$zonas], 200);
        $usuarios = \App\User::select('tipo_usuario', 'zona_id','token_notificacion')
            ->where('tipo_usuario', 2)->
            whereIn('zona_id',$zonas)->get();
        //return response()->json(['usuarios'=>$usuarios], 200);
        for ($i=0; $i < count($usuarios); $i++) { 
            $curso = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',4)->get();            
        }

        $players=[];

        for ($i=0; $i < count($usuarios); $i++) {
            if ($usuarios[$i]->token_notificacion!="undefined"&&$usuarios[$i]->token_notificacion!=null) {
                 array_push($players,$usuarios[$i]->token_notificacion);
             } 
            
        }
        //return 1;
       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json($players, 200);
        } 
    }

    public function index_zonasp(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los usuarios clientes
       // return response()->json(['zonas'=>$zonas], 200);
        $usuarios = \App\User::select('tipo_usuario', 'zona_id','token_notificacion')
            ->where('tipo_usuario', 3)->
            whereIn('zona_id',$zonas)->get();
        //return response()->json(['usuarios'=>$usuarios], 200);
        for ($i=0; $i < count($usuarios); $i++) { 
            $curso = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('usuario_id',$usuarios[$i]->id)->where('estado',4)->get();            
        }

        $players=[];

        for ($i=0; $i < count($usuarios); $i++) {
            if ($usuarios[$i]->token_notificacion!="undefined"&&$usuarios[$i]->token_notificacion!=null) {
                 array_push($players,$usuarios[$i]->token_notificacion);
             } 
            
        }
        //return 1;
       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json($players, 200);
        } 
    }
    public function index_zonasadmin(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        $usuarios = \App\User::select('tipo_usuario', 'zona_id','token_notificacion')
            ->where('tipo_usuario', 0)->get();
        

        $players=[];

        for ($i=0; $i < count($usuarios); $i++) {
            if ($usuarios[$i]->token_notificacion!="undefined"&&$usuarios[$i]->token_notificacion!=null) {
                 array_push($players,$usuarios[$i]->token_notificacion);
             } 
            
        }
        //return 1;
       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json($players, 200);
        } 
    }

    public function index_ciudades(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los usuarios clientes
       // return response()->json(['zonas'=>$zonas], 200);
        $usuarios = \App\User::select('id', 'email', 'zona_id','token_notificacion')
            ->where('tipo_usuario', 1)->get();

        $players=[];

        for ($i=0; $i < count($usuarios); $i++) {
            if ($usuarios[$i]->token_notificacion!="undefined") {
                 array_push($players,$usuarios[$i]->token_notificacion);
             } 
            
        }
        //return 1;
       // $calificaciones = \App\Calificacion::where('',$usuarios[$i]->id);
        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen usuarios.'], 404);          
        }else{
            return response()->json($players, 200);
        } 
    }



    public function indexRepartidores()
    {
        //cargar todos los usuarios repartidores
        $usuarios = \App\User::select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion')
            ->with(['chat_repartidor' => function ($query) {
                $query->select('id', 'admin_id', 'usuario_id');
            }])
            ->where('tipo_usuario', 3)->get();

        if(count($usuarios) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['usuarios'=>$usuarios], 200);
        } 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('email') || !$request->input('nombre') ||
            !$request->input('tipo_usuario') || !$request->input('tipo_registro') ||
            /*!$request->input('ciudad') || !$request->input('estado') ||*/ !$request->input('telefono') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            //return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 
        
        $aux = \App\User::where('email', $request->input('email'))->get();
        if(count($aux)!=0){

            if ($request->input('tipo_registro') == 1 || $request->input('tipo_registro') == 3) {
                //Devolvemos un código 409 Conflict. 
                return response()->json(['error'=>'Ya existe un usuario con esas credenciales.'], 409);
            }else{

                $auxUser = $aux[0];
                $auxUser->email = $request->input('email');
                //$auxUser->password = Hash::make($request->input('password'));
                $auxUser->nombre = $request->input('nombre');
                $auxUser->ciudad = $request->input('ciudad');
                $auxUser->pais_id = $request->input('pais_id');
                $auxUser->estado = $request->input('estado');
                $auxUser->telefono = $request->input('telefono');
                $auxUser->confirmado = $request->input('confirmado');
                $auxUser->intentos = $request->input('intentos');
                $auxUser->imagen = $request->input('imagen');
                $auxUser->zona_id = $request->input('zona_id');
                $auxUser->tipo_registro = $request->input('tipo_registro');
                $auxUser->validado = 1; //autovalidar el usuario

                if ($request->input('tipo_registro') == 2) {
                    $auxUser->id_facebook = $request->input('id_facebook');
                }else if ($request->input('tipo_registro') == 3) {
                    $auxUser->id_twitter = $request->input('id_twitter');
                }else if ($request->input('tipo_registro') == 4) {
                    $auxUser->id_instagram = $request->input('id_instagram');
                }

                if ($request->has('token_notificacion')) {
                    if ($request->input('token_notificacion') != 'null' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != '') {

                        $auxUser->token_notificacion = $request->input('token_notificacion');
                    }
                }
                
                // Almacenamos en la base de datos el registro.
                if ($auxUser->save()) {

                    $this->emailDeValidacion($usuario->email);

                    $Notificacion= new \App\Notificacion;
                    $Notificacion->mensaje='Nuevo Usuario registrado '.$usuario->email;
                    $Notificacion->usuario_id=$auxUser->id;
                    $Notificacion->accion=3;
                    
                    try {
                        $Notificacion->save();
                    } catch (Exception $e) {
                        //return response()->json(['error'=>$e], 500);
                    }

                    if (!$token = JWTAuth::fromUser($auxUser)) {
                        return response()->json(['error' => 'could_not_create_token'], 401);
                    }

                    $auxUser = JWTAuth::toUser($token);
                    
                    return response()->json(['message'=>'Usuario actualizado con éxito.', 'usuario'=>$auxUser, 'token' => $token], 200);
                }else{
                    return response()->json(['error'=>'Error al actualizar el usuario.'], 500);
                }
                
                
            }
            
        }

        if ($request->input('tipo_registro') == 1) {
            $validado = 1;
        }else{
            $validado = 1;
        }

        /*Primero creo una instancia en la tabla usuarios*/
        $usuario = new \App\User;
        $usuario->email = $request->input('email');

        if ($request->input('password') != null && $request->input('password') != '')
        {
            $usuario->password = Hash::make($request->input('password'));
        }

        $usuario->nombre = $request->input('nombre');
        $usuario->ciudad = $request->input('ciudad');
        $usuario->pais_id = $request->input('pais_id');
        $usuario->estado = $request->input('estado');
        $usuario->telefono = $request->input('telefono');
        $usuario->imagen = $request->input('imagen');
        $usuario->tipo_usuario = $request->input('tipo_usuario');
        $usuario->tipo_registro = $request->input('tipo_registro');
        $usuario->id_facebook = $request->input('id_facebook');
        $usuario->id_twitter = $request->input('id_twitter');
        $usuario->id_instagram = $request->input('id_instagram');
        $usuario->zona_id = $request->input('zona_id');
        $usuario->validado = $validado;
        $usuario->status = 'ON';

        if ($request->has('token_notificacion')) {
            if ($request->input('token_notificacion') != 'null' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != '') {

                $usuario->token_notificacion = $request->input('token_notificacion');
            }
        }

        if($usuario->save()){

            //Si es un registro con normal con email y password enviar correo de verificacion
            //if ($usuario->tipo_registro == 1) {
                $this->emailDeValidacion($usuario->email);
                $Notificacion= new \App\Notificacion;
                    $Notificacion->mensaje='Nuevo Usuario registrado '.$usuario->email;
                    $Notificacion->usuario_id=$usuario->id;
                    $Notificacion->accion=3;
                    
                    try {
                        $Notificacion->save();
                    } catch (Exception $e) {
                        //return response()->json(['error'=>$e], 500);
                    }
            //}

            if (!$token = JWTAuth::fromUser($usuario)) {
                    return response()->json(['error' => 'could_not_create_token'], 401);
                }

                $usuario = JWTAuth::toUser($token);

           return response()->json(['message'=>'Usuario creado con éxito.', 'usuario'=>$usuario, 'token' => $token], 200);
        }else{
            return response()->json(['error'=>'Error al crear el usuario.'], 500);
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
        //cargar un usuario
        $usuario = \App\User::find($id);

        if(count($usuario)==0){
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);          
        }else{

            return response()->json(['usuario'=>$usuario], 200);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    
    public function usuarios_cerrar_sesion(Request $request, $id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }  

        if ($token_notificacion != null && $token_notificacion!='')
        {
            $usuario->token_notificacion = '';
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($usuario->save()) {
                    
                return response()->json(['message'=>'Usuario actualizado con éxito.', 'usuario'=>$usuario], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el usuario.'], 500);
            }
            
        }
        else
        {
            
            return response()->json(['error'=>'No se ha modificado ningún dato del usuario.'],409);
        }

    }
    public function update(Request $request, $id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $email=$request->input('email'); 
        $password=$request->input('password');  
        $nombre=$request->input('nombre');
        $ciudad = $request->input('ciudad');
        $estado = $request->input('estado');
        $telefono = $request->input('telefono');
        $imagen=$request->input('imagen');
        $tipo_usuario=$request->input('tipo_usuario');
        $tipo_registro=$request->input('tipo_registro');
        $zona_id=$request->input('zona_id');
        $validado=$request->input('validado');
        $token_notificacion=$request->input('token_notificacion');
        $status=$request->input('status');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($email != null && $email!='')
        {
            $aux = \App\User::where('email', $request->input('email'))
            ->where('id', '<>', $usuario->id)->get();

            if(count($aux)!=0){
               // Devolvemos un código 409 Conflict. 
                return response()->json(['error'=>'Ya existe otro usuario con ese email.'], 409);
            }

            $usuario->email = $email;
            $bandera=true;
        }

        if ($password != null && $password!='')
        {
            $usuario->password = Hash::make($request->input('password'));
            $bandera=true;
        }

        if ($nombre != null && $nombre!='')
        {
            $usuario->nombre = $nombre;
            $bandera=true;
        }

        if ($zona_id != null && $zona_id!='')
        {
            $usuario->zona_id = $zona_id;
            $bandera=true;
        }

        if ($ciudad != null && $ciudad!='')
        {
            $usuario->ciudad = $ciudad;
            $bandera=true;
        }

        if ($estado != null && $estado!='')
        {
            $usuario->estado = $estado;
            $bandera=true;
        }

        if ($telefono != null && $telefono!='')
        {
            $usuario->telefono = $telefono;
            $bandera=true;
        }

        if ($imagen != null && $imagen!='')
        {
            $usuario->imagen = $imagen;
            $bandera=true;
        }

        if ($tipo_usuario != null && $tipo_usuario!='')
        {
           // $usuario->tipo_usuario = $tipo_usuario;
            $bandera=true;
        }

        if ($tipo_registro != null && $tipo_registro!='')
        {
            $usuario->tipo_registro = $tipo_registro;
            $bandera=true;
        }

        if ($validado != null && $validado!='')
        {
            $usuario->validado = $validado;
            $bandera=true;
        }

        if ($token_notificacion != null && $token_notificacion!='')
        {
            $usuario->token_notificacion = $token_notificacion;
            $bandera=true;
        }

        if ($status != null && $status!='')
        {
            $usuario->status = $status;
            $bandera=true;
        }


        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($usuario->save()) {
                    /*$Notificacion= new \App\Notificacion;
                    $Notificacion->mensaje='Nuevo Usuario registrado '.$usuario->email;
                    $Notificacion->usuario_id=$usuario->id;
                    $Notificacion->accion=3;
                    
                    try {
                        $Notificacion->save();
                    } catch (Exception $e) {
                        //return response()->json(['error'=>$e], 500);
                    }*/
                return response()->json(['message'=>'Usuario actualizado con éxito.', 'usuario'=>$usuario], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el usuario.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del usuario.'],409);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        $pedidos = $usuario->pedidos;

        if (sizeof($pedidos) > 0)
        {
            // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'Este usuario no puede ser eliminado porque posee pedidos asociados.'], 409);
        }

        //Eliminamos los comentarios en los blogs
        DB::table('msgs_blogs')->where('usuario_id', $id)->delete();

        //Eliminamos el chat si lo tiene
        $chat = $usuario->chat_cliente;
        if (sizeof($chat) > 0) {

            $mensajes = $chat->mensajes;

            if (sizeof($mensajes) > 0)
            {
                for ($i=0; $i < count($mensajes) ; $i++) { 
                    $mensajes[$i]->delete();
                }
            }

            // Eliminamos el chat.
            $chat->delete();
        }

        // Eliminamos el usuario.
        $usuario->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el usuario.'], 200);
    }

    public function validarCuenta($email)
    {
        //cargar un usuario
        $usuario = \App\User::where('email', $email)->get();

        if(count($usuario)==0){
            return response()->view('validar_cuenta.404', [], 404);
            //return response()->json(['error'=>'No existe el usuario con email '.$email], 404);          
        }else{

            if ($usuario[0]->validado == 1) {
                return response()->view('validar_cuenta.200', [], 200);
                //return response()->json(['message'=>'Cuenta validada con éxito.'], 200);
            }

            $usuario[0]->validado = 1;

            if ($usuario[0]->save()) {
                return response()->view('validar_cuenta.200', [], 200);
                //return response()->json(['message'=>'Cuenta validada con éxito.'], 200);
            }else{
                return response()->view('validar_cuenta.500', [], 500);
                //return response()->json(['error'=>'Error al validar la cuenta.'], 500);
            }
        }
    }

    public function emailDeValidacion($email)
    {
        //$enlace = 'http://localhost/gitHub/Mouvers/mouversAPI/public/usuarios/validar/'.$email;

        //$enlace = 'http://mouvers.mx/mouversAPI/public/usuarios/validar/'.$email;
        $enlace = 'https://service24.app/apii/public/usuarios/validar/'.$email;

        //return response()->view('emails.validar_cuenta', ['enlace' => $enlace], 200);

        $data = array( 'enlace' => $enlace);

        //Enviamos el correo con el enlace para validar
        Mail::send('emails.validar_cuenta', $data, function($msj) use ($email){
            $msj->subject('Validar cuenta Service24');
            $msj->from('service24@gmail.com', 'Service24');
            $msj->to($email);
        });
    }

    public function verificar_numero(Request $request, $numero)
    {
        if ($request->input('codigo')==null||$request->input('codigo')=='') {
            
            $salt = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';

            $rand = '';
            $i = 0;
            $length = 6;

            while ($i < $length) {
                //Loop hasta que el string aleatorio contenga la longitud ingresada.
                $num = rand() % strlen($salt);
                $tmp = substr($salt, $num, 1);
                $rand = $rand . $tmp;
                $i++;
            }
        }else{
            $rand = $request->input('codigo');
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/sms/sms.php?numero=".$numero."&codigo=".$rand);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        return response()->json(['codigo'=>$rand,'numero'=>$numero], 200);
https://service24.app/sms/sms.php?numero=584125485409&codigo=123
    }

    public function webcontacto($email,Request $request)
    {
        //Enviamos el correo con el enlace para validar
        //$enlace = 'https://service24.app/alinstanteAPI/public/usuarios/validar/';

        $data = array( 'email' => $email,'name' => $request->input('name'),'phone' => $request->input('phone'),'msg' => $request->input('msg'));

        Mail::send('emails.contactoweb', $data, function($msj) use ($email){
            $msj->subject('Gracias por contactarnos Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to($email);
        });

        Mail::send('emails.tehancontactado', $data, function($msj) use ($email){
            $msj->subject('!Te han contactado! Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to('service24uy@gmail.com');
        });
    }

    public function webcontactopanama($email,Request $request)
    {
        //Enviamos el correo con el enlace para validar
        //$enlace = 'https://service24.app/alinstanteAPI/public/usuarios/validar/';

        $data = array( 'email' => $email,'name' => $request->input('name'),'phone' => $request->input('phone'),'msg' => $request->input('msg'));

        Mail::send('emails.contactoweb', $data, function($msj) use ($email){
            $msj->subject('Gracias por contactarnos Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to($email);
        });

        Mail::send('emails.tehancontactado', $data, function($msj) use ($email){
            $msj->subject('!Te han contactado! Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to('info@service24panama.com');
        });
    }

    public function webcontactoproveedor($email,Request $request)
    {
        //Enviamos el correo con el enlace para validar
        //$enlace = 'https://service24.app/alinstanteAPI/public/usuarios/validar/';

        $data = array( 'email' => $email,'name' => $request->input('name'),'phone' => $request->input('phone'),'msg' => $request->input('msg'));

        Mail::send('emails.contactoweb', $data, function($msj) use ($email){
            $msj->subject('Gracias por contactarnos Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to($email);
        });

        Mail::send('emails.tehancontactadoproveedor', $data, function($msj) use ($email){
            $msj->subject('¡Te han contactado! Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to('service24uy@gmail.com');
        });
    }
    public function webcontactoproveedorpanama($email,Request $request)
    {
        //Enviamos el correo con el enlace para validar
        //$enlace = 'https://service24.app/alinstanteAPI/public/usuarios/validar/';

        $data = array( 'email' => $email,'name' => $request->input('name'),'phone' => $request->input('phone'),'msg' => $request->input('msg'));

        Mail::send('emails.contactoweb', $data, function($msj) use ($email){
            $msj->subject('Gracias por contactarnos Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to($email);
        });

        Mail::send('emails.tehancontactadoproveedor', $data, function($msj) use ($email){
            $msj->subject('¡Te han contactado! Service24');
            $msj->from('service24uy@gmail.com', 'Service24');
            $msj->to('info@service24panama.com');
        });
    }

    public function misPedidosHistorial($id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        //cargar todos los pedidos
        $pedidos = \App\Pedido::where('usuario_id', $id)
            ->with('productos.establecimiento')
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos registrados en historial.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function misPedidosHoy($id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        //cargar los pedidos de hoy
        $pedidos = \App\Pedido::where('usuario_id', $usuario->id)
            ->where(DB::raw('DAY(created_at)'),DB::raw('DAY(now())'))
            ->where(DB::raw('MONTH(created_at)'),DB::raw('MONTH(now())'))
            ->where(DB::raw('YEAR(created_at)'),DB::raw('YEAR(now())'))
            ->with('productos.establecimiento')
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos registrados hoy.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function misPedidosEncurso($id)
    {

        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        //cargar todos los pedidos en curso (Estado 1, 2, 3)
        $pedidos = \App\Pedido::with('productos.subcategoria.categoria')
            ->with('productos.establecimiento')
            ->with('repartidor.usuario')
            ->where('usuario_id', $id)
            ->where('estado_pago','aprobado')
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3);
            })
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos en curso.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function misPedidosFinalizados($id)
    {

        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        //cargar todos los pedidos en curso (Estado 1, 2, 3)
        $pedidos = \App\Pedido::where('usuario_id', $id)
            ->with('productos.subcategoria.categoria')
            ->with('productos.establecimiento')
            ->with('repartidor.usuario')
            ->with('calificacion')
            //->where('estado',4)
            //->orWhere('estado',5)
            ->whereIn('estado', [4,5])
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos finalizados.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function setTokenNotificaion(Request $request, $id)
    {
        // Comprobamos si el usuario que nos están pasando existe o no.
        $usuario=\App\User::find($id);

        if (count($usuario)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el usuario con id '.$id], 404);
        }

        $token_notificacion=$request->input('token_notificacion');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($token_notificacion != null && $token_notificacion!='')
        {
            $usuario->token_notificacion = $token_notificacion;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($usuario->save()) {
                return response()->json(['message'=>'Toke de notificación actualizado con éxito.', 'usuario'=>$usuario], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el usuario.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del usuario.'],409);
        }
    }
}
