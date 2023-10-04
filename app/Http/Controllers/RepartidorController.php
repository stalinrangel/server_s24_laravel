<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use Exception;

class RepartidorController extends Controller
{

    public function ciudad($ciudad_id)
    {
        $ciudad = \App\Ciudad::with('zonas')->get();
        $zonas=[];
        return $ciudad;
        for ($i=0; $i < count($ciudad); $i++) { 
            if ($ciudad[$i]->id==$ciudad_id) {
                for ($j=0; $j < count($ciudad[$i]->zonas); $j++) { 
                    array_push($zonas,$ciudad[$i]->zonas[$j]->id);
                }
            }
        }
        return $zonas;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {  // return 1;
        //$zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores
        //return 1;
        $repartidores = \App\Repartidor::select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')
            ->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario','zona_id', 'token_notificacion','created_at')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',2)
                            ->orWhere('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato')
                    ->with('Calificacion');
                }])
            ->with('establecimiento.productos.subcategoria.categoria.catprincipales')
            /*->with(['establecimiento.productos' => function ($query){
                    $query->with('zonas')
                    ->with('subcategoria.categoria.catprincipales');
               }])*/
            ->with('calificaciones.producto.pedidos.usuario')
            ->with('establecimiento.productos')
            //->whereIn('zona_id',$zonas)
            ->whereIn('activo',[1,2])
            ->orderBy('id', 'desc')->get();

            //return 1;
        $aux=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            try{
                if ($repartidores[$i]->usuario->registro!=null) {
                    array_push($aux,$repartidores[$i]);
                }
            }catch(Exception $e){

            }    
        }
        $repartidores=$aux;
        for ($i=0; $i < count($repartidores); $i++) { 
            $curso = \App\Pedido::where('repartidor_id',$repartidores[$i]->id)->where('estado',3)->get();
            $final = \App\Pedido::where('repartidor_id',$repartidores[$i]->id)->where('estado',4)->get();

            $repartidores[$i]->encurso=count($curso);
            $repartidores[$i]->enfinalizados=count($final);


            $calificaciones = \App\Calificacion::where('califique_a',$repartidores[$i]->usuario_id)->get();
           
            
            if (count($calificaciones)!=0)
            {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio=$promedio+$calificaciones[$j]->puntaje;
                }
                $promedio=$promedio/count($calificaciones);
                $repartidores[$i]->promedio=$promedio;
            }else{
                $repartidores[$i]->promedio=0;
            } 
            
        }

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }

    
    public function index_sin_registro(Request $request)
    {  // return 1;
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores

        $repartidores = \App\Repartidor::select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')
            ->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario','zona_id', 'token_notificacion','created_at')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',2)
                            ->orWhere('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    /*->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])*/
                    //->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato');
                    //->with('Calificacion');
                }])
            //->with('establecimiento.productos.subcategoria.categoria.catprincipales')
            /*->with(['establecimiento.productos' => function ($query){
                    $query->with('zonas')
                    ->with('subcategoria.categoria.catprincipales');
               }])*/
            //->with('calificaciones.producto.pedidos.usuario')
            //->with('establecimiento.productos.zonas2')
            ->whereIn('zona_id',$zonas)
            ->orderBy('id', 'desc')->get();

           // return 1;
        $aux=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            try{
                if ($repartidores[$i]->activo==1||$repartidores[$i]->activo==2) {
                    if ($repartidores[$i]->usuario->registro==null) {
                        array_push($aux,$repartidores[$i]);
                    }
                }
                
            }catch(Exception $e){

            }    
        }
        $repartidores=$aux;
        for ($i=0; $i < count($repartidores); $i++) { 
            $curso = \App\Pedido::where('repartidor_id',$repartidores[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('repartidor_id',$repartidores[$i]->id)->where('estado',4)->get();

            $repartidores[$i]->encurso=count($curso);
            $repartidores[$i]->enfinalizados=count($final);


            $calificaciones = \App\Calificacion::where('califique_a',$repartidores[$i]->id)->get();
           
            
            if (count($calificaciones)!=0)
            {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio=$promedio+$calificaciones[$j]->puntaje;
                }
                $promedio=$promedio/count($calificaciones);
                $repartidores[$i]->promedio=$promedio;
            }else{
                $repartidores[$i]->promedio=0;
            } 
            
        }

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }

    public function reporte(Request $request)
    {  // return 1;
        $zonas=$this->ciudad($request->input('ciudad_id'));

        //cargar todos los repartidores
        $repartidores = \App\Repartidor::select('id', 'estado', 'activo','ocupado','usuario_id','plan')
            ->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion','created_at')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',2)
                            ->orWhere('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    });
    
                }])->whereIn('zona_id',$zonas)->orderBy('id', 'desc')->get();

           // return 1;
        $aux=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            if ($repartidores[$i]->usuario->registro!=null) {
                array_push($aux,$repartidores[$i]);
            }
        }
        $repartidores=$aux;
        for ($i=0; $i < count($repartidores); $i++) { 
            $plan=json_decode($repartidores[$i]->plan);
             try {
                    if ($plan->tipo_plan) {                    
                        $plan2=$plan->tipo_plan;
                        $plan3=$plan->costo;
                        //$plan4=$plan2+' '+$plan3;
                        $repartidores[$i]->nombre=$repartidores[$i]->usuario->nombre;
                        $repartidores[$i]->tipoplan=$plan2;
                        $repartidores[$i]->costo_plan=$plan3;
                        $repartidores[$i]->email=$repartidores[$i]->usuario->email;
                        $repartidores[$i]->telefono=$repartidores[$i]->usuario->telefono;
                    }
                } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
                }
            
        } 

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }

    public function enviarNotificacion($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/alinstanteAPI/public/onesignal.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);
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
        if ( !$request->input('email') || !$request->input('password') ||
            !$request->input('nombre') || !$request->input('telefono') ||
            !$request->input('ciudad') || !$request->input('estado') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 
        
        $aux = \App\User::where('email', $request->input('email'))->get();
        if(count($aux)!=0){
            return response()->json(['error'=>'Ya existe un usuario con esas credenciales.'], 409);    
        }

        /*Primero creo una instancia en la tabla usuarios*/
        $usuario = new \App\User;
        $usuario->email = $request->input('email');
        $usuario->password = Hash::make($request->input('password'));
        $usuario->nombre = $request->input('nombre');
        $usuario->ciudad = $request->input('ciudad');
        $usuario->estado = $request->input('estado');
        $usuario->telefono = $request->input('telefono');
        $usuario->zona_id = $request->input('zona_id');
        $usuario->imagen = 'https://api.alinstante.app/terminos/imgs/user-white.png';
        $usuario->tipo_usuario = 3;
        $usuario->tipo_registro = 1;
        //$usuario->id_facebook = $request->input('id_facebook');
        //$usuario->id_twitter = $request->input('id_twitter');
        //$usuario->id_instagram = $request->input('id_instagram');
        $usuario->validado = 1;
        $usuario->status = 'ON';

        if($usuario->save()){
            /*Segundo creo una instancia en la tabla repartidores*/
            $repartidor = new \App\Repartidor;
            $repartidor->estado = 'ON';
            $repartidor->activo = 3;
            $repartidor->ocupado = 2;
            $repartidor->usuario_id = $usuario->id; 
            $repartidor->plan = $request->input('plan');
            $repartidor->zona_id = $request->input('zona_id');
            $repartidor->save();

           return response()->json(['message'=>'Repartidor creado con éxito.', 'user_repartidor'=>$usuario], 200);
        }else{
            return response()->json(['error'=>'Error al crear el repartidor.'], 500);
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
        //cargar un repartidor
        $repartidor = \App\Repartidor::with('usuario.registro')->find($id);

        if(count($repartidor)==0){
            return response()->json(['error'=>'No existe el repartidor con id '.$id], 404);          
        }else{

            return response()->json(['repartidor'=>$repartidor], 200);
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
    public function update(Request $request, $id)
    {
        // Comprobamos si el repartidor que nos están pasando existe o no.
        $repartidor = \App\Repartidor::find($id);
        $usuario = \App\User::find($repartidor->usuario_id);

        if (count($repartidor)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el Proveedor con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $email=$request->input('email'); 
        $password=$request->input('password');  
        $nombre=$request->input('nombre');
        $ciudad = $request->input('ciudad');
        $estado = $request->input('estado');
        $telefono = $request->input('telefono');
        $imagen=$request->input('imagen');
        //$tipo_usuario=$request->input('tipo_usuario');
        //$tipo_registro=$request->input('tipo_registro');
        //$codigo_verificacion=$request->input('codigo_verificacion');
        //$validado=$request->input('validado');
        $lat=$request->input('lat');
        $lng=$request->input('lng');
        $rep_estado=$request->input('rep_estado');
        $activo=$request->input('activo');
        $ocupado=$request->input('ocupado');
        $plan=$request->input('plan');
        $firma=$request->input('firma');

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

            /*cargar los pedidos asociados al repartidor para actualizar la variable repartidor_nom*/
            $pedidos = \App\Pedido::where('repartidor_id',$repartidor->id)->get();

            for ($i=0; $i < count($pedidos) ; $i++) { 
                $pedidos[$i]->repartidor_nom = $nombre;
                $pedidos[$i]->save();
            }

            $usuario->nombre = $nombre;
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

        if ($lat != null && $lat!='')
        {
            $repartidor->lat = $lat;
            $bandera=true;
        }

        if ($lng != null && $lng!='')
        {
            $repartidor->lng = $lng;
            $bandera=true;
        }

        if ($rep_estado != null && $rep_estado!='')
        {
            $repartidor->estado = $rep_estado;
            $bandera=true;
        }

        if ($activo != null && $activo!='')
        {
            $repartidor->activo = $activo;
            $bandera=true;

                $Notificacion= new \App\Notificacion;
                $act='Activo.';
                if ($activo==2) {
                    $act='Inactivo.';
                }
                $Notificacion->mensaje=''.$usuario->nombre.' ha cambiado su estado '. $act;
                $Notificacion->usuario_id=$usuario->id;
                $Notificacion->accion=4;


                $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $usuario->ciudad)
                   ->first();
                
                    
                    try {
                        //$Notificacion->save();
                    } catch (Exception $e) {
                        //return response()->json(['error'=>$e], 500);
                    }

                    try {
                        if ($admin) {

                            $obj = null;

                            $order   = array("\r\n", "\n", "\r", " ", "&");
                            $replace = array('%20', '%20', '%20', '%20', '%26');

                            $nombre_aux = str_replace($order, $replace, $usuario->nombre);

                            $this->enviarNotificacion($admin->token_notificacion, 'Proveedor%20'.$nombre_aux.'%20ha%20cambiado%20su%20estado%20'. $act, 'null', 6, $obj);
                        }
                        
                    } catch (Exception $e) {
                        //return response()->json(['error'=>$e], 500);
                    }
        }

        if ($ocupado != null && $ocupado!='')
        {
            $repartidor->ocupado = $ocupado;
            $bandera=true;
        }

        if ($plan != null && $plan!='')
        {
            $repartidor->plan = $plan;
            $bandera=true;
        }

        if ($firma != null && $firma!='')
        {
            $repartidor->firma = $firma;
            $bandera=true;
        }



        if ($bandera)
        {
            $obj=null;
            // Almacenamos en la base de datos el registro.
            if ($repartidor->save() && $usuario->save()) {

                 $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $usuario->usuario)
                   ->first();

                 $obj = null;

                 if ($admin) {
                    $this->enviarNotificacion($admin->token_notificacion, 'Proveedor%20ha%20cambiado%20su%20estado.', 'null', 6, $obj);
                 }
                

                if ($activo==2) {

                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    $newstr = str_replace($order, $replace, $request->input('msg'));

                    $oneSignal = $this->enviarNotificacion($usuario->token_notificacion, $newstr, 'null', 9, $obj);
                        
                }

               
                return response()->json(['message'=>'Proveedor actualizado con éxito.', 'repartidor'=>$repartidor], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el Proveedor.'], 500);
            }
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del Proveedor.'],409);
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
        // Comprobamos si el repartidor que nos están pasando existe o no.
        $repartidor=\App\Repartidor::find($id);

        if (count($repartidor)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el Proveedor con id '.$id], 404);
        }

        $pedidos = $repartidor->pedidos;

        for ($i=0; $i < count($pedidos) ; $i++) {
            if ($pedidos[$i]->estado == 2 || $pedidos[$i]->estado == 3) {
                return response()->json(['error'=>'No se puede eliminar el Proveedor porque posee pedidos en curso.'], 409);
             } 
        }

        for ($i=0; $i < count($pedidos) ; $i++) { 
            $pedidos[$i]->repartidor_id = null;
            $pedidos[$i]->save();
        }

        $usuario=\App\User::find($repartidor->usuario_id);

        //Eliminamos el chat si lo tiene
        $chat = $usuario->chat_repartidor;
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

        // Eliminamos el repartidor.
        $repartidor->delete();

        // Eliminamos el usuario del repartidor.
        $usuario->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el Proveedor.'], 200);
    }

    public function setPosicion(Request $request, $id)
    {
        // Comprobamos si el repartidor que nos están pasando existe o no.
       // $repartidor = \App\Repartidor::find($id);

        if (count($repartidor)==0)
        {
            // Devolvemos error codigo http 404
            //return response()->json(['error'=>'No existe el Proveedor con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $lat=$request->input('lat');
        $lng=$request->input('lng');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($lat != null && $lat!='')
        {
            $repartidor->lat = $lat;
            $bandera=true;
        }

        if ($lng != null && $lng!='')
        {
            $repartidor->lng = $lng;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            $Establecimiento = \App\Establecimiento::where('usuario_id',$id)->first();

            if (count($Establecimiento)==0)
            {
                // Devolvemos error codigo http 404
                return response()->json(['error'=>'No existe el Establecimiento con el usuario id '.$id], 404);
            }      

            $Establecimiento->lat = $lat;
            $Establecimiento->lng = $lng;
            
            if ($Establecimiento->save()) {
                return response()->json(['message'=>'ok.'], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el Proveedor.'], 500);
            }
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del Proveedor.'],409);
        }
    }
    public function posicion($id)
    {
        //cargar un repartidor
        $repartidor = \App\Repartidor::with('establecimiento')->find($id);

        if(count($repartidor)==0){
            return response()->json(['error'=>'No existe el repartidor con id '.$id], 404);          
        }else{

            return response()->json(['lat'=>$repartidor->establecimiento->lat,'lng'=>$repartidor->establecimiento->lng], 200);
        }
    }

    public function repDisponibles(Request $request)
    {

        //cargar los productos de la zona y subcat
        $productos = \App\Producto::where('zona_id', $request->input('zona_id'))
            ->where('subcategoria_id', $request->input('subcategoria_id'))
            ->with('establecimiento')
            ->get();

        if(count($productos) == 0){
            return response()->json(['error'=>'No hay proveedores disponibles.'], 404);          
        }else{

            $idsAux = [$productos[0]->establecimiento->usuario_id];

            if (count($productos) > 1) {

                for ($i=1; $i < count($productos); $i++) { 
                    $esta = false;
                    for ($j=0; $j < count($idsAux); $j++) { 
                        if ($productos[$i]->establecimiento->usuario_id == $idsAux[$j]) {
                            $esta = true;
                        }
                    }
                    if (!$esta) {
                        array_push($idsAux, $productos[$i]->establecimiento->usuario_id);
                    }
                }

            }

            //cargar todos los repartidores en ON, Trabajando y Disponibles
            $repartidores = \App\Repartidor::where('estado', 'ON')
                    ->where('activo', 1)
                    //->where('ocupado', 2)
                    ->with('usuario')
                    ->whereIn('usuario_id', $idsAux)
                    ->has('usuario')
                    ->get();

            if(count($repartidores) == 0){
                return response()->json(['error'=>'No hay proveedores disponibles.'], 404);          
            }else{
                return response()->json(['repartidores'=>$repartidores], 200);
            }    
            
        } 

         
    }

    public function miPedidoEnEspera($repartidor_id)
    {
        //cargar todos los pedidos en curso (Estado 2, 3)
        $pedido = \App\Pedido::with('usuario')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            ->where('repartidor_id', $repartidor_id)
            ->where(function ($query) {
                $query->where('estado', 2);
            })
            ->get();

        if(count($pedido) == 0){
            return response()->json(['error'=>'No tienes pedido en curso.'], 404);          
        }else{
            return response()->json(['pedido'=>$pedido], 200);
        } 
    }

    /*Retorna el pedido en curso de un repartidor_id*/
    public function miPedidoEnCurso($repartidor_id)
    {
        //cargar todos los pedidos en curso (Estado 2, 3)
        $pedido = \App\Pedido::with('usuario')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            ->where('repartidor_id', $repartidor_id)
            ->where(function ($query) {
                $query->where('estado', 3);
            })
            ->get();

        if(count($pedido) == 0){
            return response()->json(['error'=>'No tienes pedido en curso.'], 404);          
        }else{
            return response()->json(['pedido'=>$pedido], 200);
        } 
    }

    /*retorna el historial de los pedidos de un
    repartidor_id filtrados por fecha*/
    public function historialPedidos(Request $request, $repartidor_id)
    {
        //cargar todos los pedidos
        $pedidos = \App\Pedido::
            where('repartidor_id', $repartidor_id)
            //->where(DB::raw('DAY(created_at)'),1)
            //->where('estado',4)
            //->orWhere('estado',5)
            ->whereIn('estado', [4, 5])
            ->with('usuario')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            //->with('calificacion')
           
            //->where(DB::raw('MONTH(created_at)'),$request->input('mes'))
            //->where(DB::raw('YEAR(created_at)'),$request->input('anio'))
            ->get();

        for ($i=0; $i < count($pedidos); $i++) {
            $calificacion=\App\Calificacion::where('pedido_id',$pedidos[$i]->id)/*->where('califique_a',$pedidos[$i]->usuario->id)*/->with('usuario')->with('producto')->get();
            $pedidos[$i]->calificacion=$calificacion;
        }

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos registrados en la fecha '.$request->input('mes').'/'.$request->input('anio')], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    /*Retorna el conteo de pedidos en curso  
    y finalizados de un repartidor_id*/
    public function conteoPedidos($repartidor_id)
    {
        //contar todos los pedidos en curso (Estado 1 2 3)
        $enCurso = \App\Pedido::
            where('repartidor_id',$repartidor_id)
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3);
            })
            ->count();

        //contar todos los pedidos en finalizados (Estado 4)
        $enFinalizados = \App\Pedido::
            where('repartidor_id',$repartidor_id)
            ->where('estado',4)
            ->count();

        return response()->json(['enCurso'=>$enCurso, 'enFinalizados'=>$enFinalizados], 200);
         
    }

    /*
    Verifica el estado del registro de los proveedores
    Caso A sin registro
    Caso B registro incompleto
    */
    public function estadoDelRegistro(Request $request)
    {  
        //$zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores

        $repartidores = \App\Repartidor::select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')
            ->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario','zona_id', 'token_notificacion','created_at')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',2)
                            ->orWhere('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato')
                    ->with('Calificacion');
                }])
            ->with('establecimiento.productos.subcategoria.categoria.catprincipales')
            /*->with(['establecimiento.productos' => function ($query){
                    $query->with('zonas')
                    ->with('subcategoria.categoria.catprincipales');
               }])*/
            ->with('calificaciones.producto.pedidos.usuario')
            ->with('establecimiento.productos')
          //  ->whereIn('zona_id',$zonas)
            ->orderBy('id', 'desc')->get();

        $auxA=[];
        $auxB=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            try{
                if ($repartidores[$i]->usuario->tipo_usuario!=null) {
                   if ($repartidores[$i]->usuario->registro==null) {
                        array_push($auxA,$repartidores[$i]);
                    }
                    elseif ($repartidores[$i]->usuario->registro!=null) {
                        array_push($auxB,$repartidores[$i]);
                    }
                }
                
            }catch(Exception $e){

            }    
        }

        $repartidoresA=$auxA;
        $repartidoresB=$auxB;



        for ($i=0; $i < count($repartidoresB); $i++) { 
            $curso = \App\Pedido::where('repartidor_id',$repartidoresB[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('repartidor_id',$repartidoresB[$i]->id)->where('estado',4)->get();

            $repartidoresB[$i]->encurso=count($curso);
            $repartidoresB[$i]->enfinalizados=count($final);


            $calificaciones = \App\Calificacion::where('califique_a',$repartidoresB[$i]->id)->get();
           
            
            if (count($calificaciones)!=0)
            {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio=$promedio+$calificaciones[$j]->puntaje;
                }
                $promedio=$promedio/count($calificaciones);
                $repartidoresB[$i]->promedio=$promedio;
            }else{
                $repartidoresB[$i]->promedio=0;
            } 
            
        }

        if(count($repartidoresA) == 0 && count($repartidoresB) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidoresA'=>$repartidoresA,
                'repartidoresB'=>$repartidoresB], 200);
        } 
    }    


    public function estadoRevision(Request $request)
    {  
        //$zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores

        $repartidores = \App\Repartidor::select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')->where('activo',4)
            ->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario','zona_id', 'token_notificacion','created_at')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',2)
                            ->orWhere('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato')
                    ->with('Calificacion');
                }])
            ->with('establecimiento.productos.subcategoria.categoria.catprincipales')
            /*->with(['establecimiento.productos' => function ($query){
                    $query->with('zonas')
                    ->with('subcategoria.categoria.catprincipales');
               }])*/
            ->with('calificaciones.producto.pedidos.usuario')
            ->with('establecimiento.productos')
          //  ->whereIn('zona_id',$zonas)
            ->orderBy('id', 'desc')->get();

        $auxA=[];
        $auxB=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            try{
                if ($repartidores[$i]->usuario->tipo_usuario!=null) {
                   if ($repartidores[$i]->usuario->registro==null) {
                        array_push($auxA,$repartidores[$i]);
                    }
                    elseif ($repartidores[$i]->usuario->registro!=null) {
                        array_push($auxB,$repartidores[$i]);
                    }
                }
                
            }catch(Exception $e){

            }    
        }

        $repartidoresA=$auxA;
        $repartidoresB=$auxB;



        for ($i=0; $i < count($repartidoresB); $i++) { 
            $curso = \App\Pedido::where('repartidor_id',$repartidoresB[$i]->id)->where('estado',2)->get();
            $final = \App\Pedido::where('repartidor_id',$repartidoresB[$i]->id)->where('estado',4)->get();

            $repartidoresB[$i]->encurso=count($curso);
            $repartidoresB[$i]->enfinalizados=count($final);


            $calificaciones = \App\Calificacion::where('califique_a',$repartidoresB[$i]->id)->get();
           
            
            if (count($calificaciones)!=0)
            {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio=$promedio+$calificaciones[$j]->puntaje;
                }
                $promedio=$promedio/count($calificaciones);
                $repartidoresB[$i]->promedio=$promedio;
            }else{
                $repartidoresB[$i]->promedio=0;
            } 
            
        }

        if(count($repartidoresA) == 0 && count($repartidoresB) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidoresA'=>$repartidoresA,
                'repartidores'=>$repartidoresB], 200);
        } 
    }   

}
