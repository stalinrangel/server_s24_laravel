<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;
use Exception;

class PedidoController extends Controller
{   
    //Enviar notificacion a un dispositivo cliente mediante su token_notificacion
    public function enviarNotificacionProveedor($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        

        curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/oproveedor.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        //return $response;
    }
    //Enviar notificacion a un dispositivo cliente mediante su token_notificacion
    public function enviarNotificacionCliente($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/oclientes.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        //return $response;
    }

    //Enviar notificacion a un dispositivo repartidor/panel mediante su token_notificacion
    public function enviarNotificacion($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/onesignal.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todos los pedidos
        $pedidos = \App\Pedido::with('usuario')
            ->with('repartidor')
            ->with('productos.establecimiento')
            ->with('zonas')
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No existen pedidos en el historial.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
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
        // Primero comprobaremos si estamos recibiendo todos los campos obligatorios.
        if (!$request->input('lat') ||
            !$request->input('lng') ||
            !$request->input('direccion') ||
            !$request->input('gastos_envio') ||
            !$request->input('costo_envio') ||
            !$request->input('subtotal') ||
            !$request->input('costo') ||
            !$request->input('usuario_id') ||
            /*!$request->input('establecimiento_id') ||*/
           // !$request->input('productos') ||
            !$request->input('ruta'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            //return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        }
        if ($request->input('zona_id')==null||$request->input('zona_id')=='') {
            $zona_id=1;
        }else{
             $zona_id=$request->input('zona_id');
        }

        //validaciones
        $aux1 = \App\User::find($request->input('usuario_id'));
        if(count($aux1) == 0){
           // Devolvemos un código 409 Conflict. 
            //return response()->json(['error'=>'id '+$request->input('usuario_id')], 409);
        } 

        $repartidor = \App\Repartidor::where('id', $request->input('repartidor_id'))->first();
        if ($repartidor->estado != 'ON' || $repartidor->activo != 1) {
            //return response()->json(['error'=>'Este proveedor no puede recibir pedidos.'], 403);
        }
        if($aux1->status == 'OFF' || $aux1->status == ''){
           // Devolvemos un código 403 Acceso prohibido. 
            return response()->json(['error'=>'Estas desactivado para hacer pedido, contacta con soporte para solucionar el inconveniente.'], 403);
        } 

        /*$establecimiento = \App\Establecimiento::find($request->input('establecimiento_id'));
        if(count($establecimiento) == 0){
           // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'No existe el establecimiento al cual se quiere asociar el pedido.'], 409);
        }*/

        //Verificar que todos los productos del pedido existen
        $productos = json_decode($request->input('productos'));
        for ($i=0; $i < count($productos) ; $i++) { 
            $aux2 = \App\Producto::find($productos[$i]->producto_id);
            if(count($aux2) == 0){
               // Devolvemos un código 409 Conflict. 
                return response()->json(['error'=>'No existe el producto con id '.$productos[$i]->producto_id], 409);
            }   
        }    

        if(is_array($request->input('direccion'))){
            $dir = implode($request->input('direccion'));
        }else{
            $dir = $request->input('direccion');
        }

        if ($request->input('repartidor_id')==null||$request->input('repartidor_id')=='') {
            $repartidor_id=1;
        }else{
             $repartidor_id=$request->input('repartidor_id');
        }

        if($nuevoPedido=\App\Pedido::create([
            'estado'=>2,
            'lat'=>$request->input('lat'),
            'lng'=>$request->input('lng'),
            'direccion'=>$dir, 
            'distancia'=>$request->input('distancia'), 
            'tiempo'=>$request->input('tiempo'),
            'gastos_envio'=>$request->input('gastos_envio'),
            'costo_envio'=>$request->input('costo_envio'), 
            'subtotal'=>$request->input('subtotal'),
            'costo'=>$request->input('costo'),
            'usuario_id'=>$request->input('usuario_id'),
            'zona_id'=>$zona_id,
            'repartidor_id'=>$repartidor_id,
            'repartidor_nom'=>$request->input('repartidor_nom'),
            'estado_pago'=>'aprobado',
            'destinatario'=>$request->input('destinatario'),
            'telefono'=>$request->input('telefono'),
            'referencia'=>$request->input('referencia'),
            'hora'=>$request->input('hora'),
            //'horario'=>$request->input('horario'),
            ])){

            //Crear las relaciones en la tabla pivote
            for ($i=0; $i < count($productos) ; $i++) { 

                $nuevoPedido->productos()->attach($productos[$i]->producto_id, [
                    'estado_deuda' => 1,
                    'cantidad' => $productos[$i]->cantidad,
                    'precio_unitario' => $productos[$i]->precio_unitario,
                    'observacion' => $productos[$i]->observacion]);
                   
            }

            //Registrar la ruta
            $ruta = json_decode($request->input('ruta'));
            for ($i=0; $i < count($ruta) ; $i++) { 
                   $nuevoPunto=\App\Ruta::create([
                        'pedido_id'=>$nuevoPedido->id,
                        'posicion'=>$ruta[$i]->posicion,
                        'establecimiento_id'=>$ruta[$i]->establecimiento_id,
                        'estado'=>1,
                        'lat'=>$ruta[$i]->lat,
                        'lng'=>$ruta[$i]->lng,
                        'despachado'=>1 //1=no 2=si
                    ]);
            }
            $obj=$nuevoPedido->id;
            //notificacion--------------------
            $repartidor = \App\Repartidor::with('usuario')->find($request->input('repartidor_id'));
             //repartidor
             $this->enviarNotificacionProveedor($repartidor->usuario->token_notificacion, 'Nuevo%20pedido%20S00'.$nuevoPedido->id, $nuevoPedido->id, 6, $obj);
             $this->enviarNotificacionCliente($repartidor->usuario->token_notificacion, 'Nuevo%20pedido%20S00'.$nuevoPedido->id, $nuevoPedido->id, 5, $obj);
                /*$Notificacion= new \App\Notificacion;
                $Notificacion->mensaje='Nuevo Pedido S00'.$nuevoPedido->id;
                $Notificacion->usuario_id=$nuevoPedido->usuario_id;
                $Notificacion->id_operacion=$nuevoPedido->id;
                $Notificacion->accion=5;
                $Notificacion->data=$obj;*/
            
            $usuario = \App\User::find($request->input('usuario_id'));
            $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $usuario->ciudad)
                   ->first();
                   
            if (true) {
                $this->enviarNotificacion($admin->token_notificacion, 'Nuevo%20pedido%20S00'.$nuevoPedido->id, $nuevoPedido->id, 6, $obj);
            }
            
                
                try {
                   // $Notificacion->save();
                } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
                }
            
            //Fin notificacion--------------------------

            return response()->json(['pedido'=>$nuevoPedido, 'message'=>'Pedido creado con éxito.'], 200);
        }else{
            return response()->json(['error'=>'Error al crear el pedido.'], 500);
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
        //cargar un pedido
        $pedido = \App\Pedido::where('id',$id)->with('usuario')
            ->with('productos.subcategoria.categoria')
            //->with('repartidor')
            ->with(['repartidor.usuario' => function ($query) {
                $query->select('usuarios.id', 'usuarios.nombre', 'usuarios.telefono', 'usuarios.imagen', 'usuarios.token_notificacion');
            }])
            ->with('productos.establecimiento')
            ->with('chat')
            ->where('estado_pago','aprobado')
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3)
                    ->orWhere('estado',4)
                    ->orWhere('estado',5);
            })->first();

        $calificaciones = \App\Calificacion::where('califique_a',$pedido->usuario_id)->get();
        $calificaciones_producto = \App\Calificacion::where('pedido_id',$pedido->id)->get();
        $pedido->calificacion=$calificaciones_producto;   
        if (count($calificaciones)!=0)
        {
            $promedio=0;
            for ($j=0; $j < count($calificaciones); $j++) { 
                $promedio=$promedio+$calificaciones[$j]->puntaje;
            }
            $promedio=$promedio/count($calificaciones);
            $pedido->usuario->promedio=$promedio;
        }else{
            $pedido->usuario->promedio=0;
        } 
        //$pedido = \App\Pedido::with('usuario')->with('productos.establecimiento')
            /*->with('establecimiento')->with('calificacion')*/
            //->with('ruta')->find($id);

        if(count($pedido)==0){
            return response()->json(['error'=>'No existe el pedido con id '.$id], 404);          
        }else{

            //$pedido->productos = $pedido->productos;
            return response()->json(['pedido'=>$pedido], 200);
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

    public function encamino($id)
    {
        $pedido=\App\Pedido::find($id);
        $pedido->encamino=1;

         if ($pedido->save()) {
                $usuario = \App\User::where('id', $pedido->usuario_id)->first();

                $this->enviarNotificacionCliente($usuario->token_notificacion, '!Proveedor%20del%20pedido%20S00'.$pedido->id.'%20en%20camino!', $pedido->id, 4, $pedido->id);

                $Notificacion= new \App\Notificacion;
                $Notificacion->mensaje='Pedido '.$pedido->id.' en camino';
                $Notificacion->usuario_id=$pedido->repartidor_id;
                $Notificacion->id_operacion=$pedido->id;
                $Notificacion->accion=6;
                
                try {
                    $Notificacion->save();
                } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
                }
                return response()->json(['pedido'=>$pedido , 'message'=>'Pedido cancelado con éxito.'], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el pedido.'], 500);
            }  
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    

    public function cancelar(Request $request, $id)
    {
        $pedido=\App\Pedido::find($id);
        $pedido->estado=5;
        $pedido->comentario=$request->input('comentario');
        $pedido->finalizo=$request->input('finalizo');

        $usuario = \App\User::where('id', $pedido->usuario_id)->with('repartidor')->first();
        $repartidor = \App\Repartidor::where('id', $pedido->repartidor_id)->first();
        $proveedor = \App\User::where('id', $repartidor->usuario_id)->first();

        //$admin = \App\User::where('tipo_usuario', 1)->first();
        $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $usuario->ciudad)
                   ->first();

        if (count($pedido)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el pedido con id '.$id], 404);
        } 

        $obj=$pedido->id;
        $this->enviarNotificacionCliente($usuario->token_notificacion, 'Cancelado%20pedido%20S00'.$pedido->id, $pedido->id, 5, $obj);
        //repartidor
        $this->enviarNotificacionProveedor($proveedor->token_notificacion, 'Cancelado%20pedido%20S00'.$pedido->id, $pedido->id, 5, $obj);
        if (true) {
            $this->enviarNotificacion($admin->token_notificacion, 'Cancelado%20pedido%20S00'.$pedido->id, $pedido->id, 5, $obj);
        }
        

        if ($pedido->save()) {

                $Notificacion= new \App\Notificacion;
                $Notificacion->mensaje='Pedido '.$pedido->id.' cancelado';
                $Notificacion->usuario_id=$request->input('finalizo');
                $Notificacion->id_operacion=$pedido->id;
                $Notificacion->accion=8;
                
                try {
                    $Notificacion->save();
                } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
                }
                return response()->json(['pedido'=>$pedido , 'message'=>'Pedido cancelado con éxito.'], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el pedido.'], 500);
            }  


    }
    public function update(Request $request, $id)
    {
                // Comprobamos si el pedido que nos están pasando existe o no.
        $pedido=\App\Pedido::find($id);

        if (count($pedido)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el pedido con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $estado=$request->input('estado');
        $lat=$request->input('lat');
        $lng=$request->input('lng');
        $direccion=$request->input('direccion'); 
        $distancia=$request->input('distancia'); 
        $tiempo=$request->input('tiempo'); 
        $costo_envio=$request->input('costo_envio');
        $subtotal=$request->input('subtotal');
        $costo=$request->input('costo');
        $repartidor_id=$request->input('repartidor_id');
        //$productos=$request->input('productos');
        $estado_pago=$request->input('estado_pago');
        $api_tipo_pago=$request->input('api_tipo_pago');
        $referencia=$request->input('referencia');
        $hora=$request->input('hora');
        $hora_aceptado=$request->input('hora_aceptado');
        //$horario=$request->input('horario'),

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($estado != null && $estado!='')
        {
            $pedido->estado = $estado;
            $bandera=true;
        }
        
        if ($hora_aceptado != null && $hora_aceptado!='')
        {
            $pedido->hora_aceptado = $hora_aceptado;
            $bandera=true;
        }

        if ($lat != null && $lat!='')
        {
            $pedido->lat = $lat;
            $bandera=true;
        }

        if ($lng != null && $lng!='')
        {
            $pedido->lng = $lng;
            $bandera=true;
        }

        if ($direccion != null && $direccion!='')
        {
            $pedido->direccion = $direccion;
            $bandera=true;
        }

        if ($distancia != null && $distancia!='')
        {
            $pedido->distancia = $distancia;
            $bandera=true;
        }

        if ($tiempo != null && $tiempo!='')
        {
            $pedido->tiempo = $tiempo;
            $bandera=true;
        }

        if ($costo_envio != null && $costo_envio!='')
        {
            $pedido->costo_envio = $costo_envio;
            $bandera=true;
        }

        if ($subtotal != null && $subtotal!='')
        {
            $pedido->subtotal = $subtotal;
            $bandera=true;
        }

        if ($costo != null && $costo!='')
        {
            $pedido->costo = $costo;
            $bandera=true;
        }

        if ($repartidor_id != null && $repartidor_id!='')
        {
            // Comprobamos si el repartidor que nos están pasando existe o no.
            $repartidor=\App\Repartidor::with('usuario')->find($repartidor_id);

            if (count($repartidor)==0)
            {
                // Devolvemos error codigo http 404
                return response()->json(['error'=>'No existe el repartidor que se quiere asociar al pedido.'], 404);
            }

            $pedido->repartidor_id = $repartidor_id;
            $pedido->repartidor_nom = $repartidor->usuario->nombre;
            $bandera=true;
        }

        if ($estado_pago != null && $estado_pago!='')
        {
            $usuario = \App\User::find($pedido->usuario_id);

            //En caso de que el pedido lo haga un cliente
            if ($usuario->tipo_usuario == 2 && ($estado_pago == 'aprobado' || $estado_pago == 'Aprobado')) {

                // Orden del reemplazo
                //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                $order   = array("\r\n", "\n", "\r", " ", "&");
                $replace = array('%20', '%20', '%20', '%20', '%26');

                //Tratar los espacios de la fecha del pedido
                $fecha = str_replace($order, $replace, $pedido->created_at);

                $obj = array('created_at'=>$fecha);
                $obj = json_encode($obj);

                //cargar los id de los estableciminetos
                $ests_id = $pedido->ruta;
                for ($i=0; $i < count($ests_id); $i++) { 

                    $est = \App\Establecimiento::select('id', 'usuario_id')
                        ->with(['usuario' => function ($query){
                            $query->select('id', 'token_notificacion');
                        }])->find($ests_id[$i]->establecimiento_id);

                    if (count($est)!=0)
                    {
                        if ($est->usuario->token_notificacion) {
                        
                            $this->enviarNotificacion($est->usuario->token_notificacion, 'Actualizado%20pedido%20S00'.$pedido->id, $pedido->id, 6, $obj);

                        }
                    } 
                }
            }

            $pedido->estado_pago = $estado_pago;
            $bandera=true;
        }

        if ($api_tipo_pago != null && $api_tipo_pago!='')
        {
            $pedido->api_tipo_pago = $api_tipo_pago;
            $bandera=true;
        }

        if ($referencia != null && $referencia!='')
        {
            $pedido->referencia = $referencia;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($pedido->save()) {
                return response()->json(['pedido'=>$pedido , 'message'=>'Pedido actualizado con éxito.'], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el pedido.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato al pedido.'],409);
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
        // Comprobamos si el pedido que nos están pasando existe o no.
        $pedido=\App\Pedido::find($id);

        if (count($pedido)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el pedido con id '.$id], 404);
        } 
       
        //Eliminar las relaciones(productos) en la tabla pivote
        $pedido->productos()->detach();

        //Eliminar la ruta
        $ruta = $pedido->ruta;
        if (sizeof($ruta) > 0)
        {
            for ($i=0; $i < count($ruta) ; $i++) { 
                $ruta[$i]->delete();
            }
        }

        // Eliminamos el pedido.
        $pedido->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el pedido.'], 200);
    }

    public function pedidosHoy()
    {
        //cargar todos los pedidos de hoy
        $pedidos = \App\Pedido::with('usuario')
            ->with('zonas')
            ->with('repartidor')
            ->with('productos.establecimiento')
            ->where(DB::raw('DAY(created_at)'),DB::raw('DAY(now())'))
            ->where(DB::raw('MONTH(created_at)'),DB::raw('MONTH(now())'))
            ->where(DB::raw('YEAR(created_at)'),DB::raw('YEAR(now())'))
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No existen pedidos para hoy.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function pedidosEncurso(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los pedidos en curso (Estado 1, 2, 3)
        $pedidos = \App\Pedido::/*whereIn('zona_id',$zonas)
            ->*/with('usuario')
            //->with('repartidor')
            ->with(['repartidor.usuario' => function ($query) {
                $query->select('usuarios.id', 'usuarios.nombre', 'usuarios.telefono');
            }])
            ->with('productos.establecimiento')
            ->with('chat')
            ->with('zonas')
            ->where('estado_pago','aprobado')
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3);
            })
            //->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->get();
       // for ($i=0; $i < count($pedidos); $i++) { 
         //   $pedidos[$i]->zonas=$pedidos[$i]->zonas2;
        //}

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No existen pedidos en curso.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function pedidosFinalizados(Request $request)
    {
         $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los pedidos en finalizados (Estado 4)
        $pedidos = \App\Pedido::/*whereIn('zona_id','1')
            ->*/with('usuario')
            //->with('repartidor')
            ->with(['repartidor'=> function ($query) {
                $query->select('id', 'estado', 'activo','ocupado','usuario_id')
                    ->with('usuario');}])
            ->with('productos.establecimiento')
            ->with('chat')
            ->with('zonas')
            ->with('calificacion')
            ->where('estado',4)
            //->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->get();

            //for ($i=0; $i < count($pedidos); $i++) { 
            //    $pedidos[$i]->zonas=$pedidos[$i]->zonas2;
            //}

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No existen pedidos finalizados.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    public function pedidosCancelados(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
            
        //cargar todos los pedidos en finalizados (Estado 4)
        $pedidos = \App\Pedido::whereIn('zona_id',$zonas)
            ->with('usuario')
            //->with('repartidor')
            ->with(['repartidor.usuario' => function ($query) {
                $query->select('usuarios.id', 'usuarios.nombre', 'usuarios.telefono');
            }])
            ->with('productos.establecimiento')
            ->with('chat')
            ->with('zonas')
            ->with('calificacion')
            ->where('estado',5)
            //->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->get();
            for ($i=0; $i < count($pedidos); $i++) { 
                $pedidos[$i]->zonas=$pedidos[$i]->zonas2;
            }

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No existen pedidos Cancelados.'], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    /*Retorna el conteo de pedidos en curso 
    y finalizados de un cliente_id*/
    public function conteoPedidos($cliente_id)
    {
        //contar todos los pedidos en curso (Estado 1 2 3)
        $enCurso = \App\Pedido::
            where('usuario_id',$cliente_id)
            ->where('estado_pago','aprobado')
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3);
            })
            ->count();

        //contar todos los pedidos en finalizados (Estado 4)
        $enFinalizados = \App\Pedido::
            where('usuario_id',$cliente_id)
            ->where('estado',4)
            ->count();

        $calificaciones = \App\Calificacion::where('califique_a',$cliente_id)->get();
            //return $calificaciones;
            if (count($calificaciones)>5) {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio+=$calificaciones[$j]->puntaje;
                }
                $promedio=round($promedio/count($calificaciones), 0, PHP_ROUND_HALF_UP); 
            }else{
              $promedio=5;  
            }

        $user->promedio_calificacion=$promedio;

        return response()->json(['enCurso'=>$enCurso, 'enFinalizados'=>$enFinalizados, 'promedio_calificacion'=>$promedio], 200);
         
    }

    /*Retorna los ultimos 10 pedidos por asignar*/
    public function pedidosPorAsignar()
    {
        //pedidos no asignados (Estado 1)
        $porAsignar = \App\Pedido::select('id', 'estado', 'usuario_id', 'repartidor_id', 'estado_pago', 'created_at')
            ->with(['usuario' => function ($query) {
                $query->select('id', 'nombre');
            }])
            ->where('estado_pago','aprobado')
            ->where('estado',1)
            ->whereNull('repartidor_id')
            ->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();


        return response()->json(['porAsignar'=>$porAsignar], 200);
         
    }

    /*Retorna la info basica de un pedido para
    balidar su estado en el panel*/
    public function infoBasica($id)
    {
        //cargar un pedido
        $pedido = \App\Pedido::select('id', 'estado', 'usuario_id', 'repartidor_id', 'estado_pago', 'created_at')
            ->find($id);

        if(count($pedido)==0){
            return response()->json(['error'=>'No existe el pedido con id '.$id], 404);          
        }else{

            return response()->json(['pedido'=>$pedido], 200);
        }
    }

    /*Retorna los pedidos de un establecimiento_id*/
    public function pedidosEst(Request $request, $establecimiento_id)
    {
        $pedidosEst = \App\Ruta::select('id', 'pedido_id')
            ->where('despachado', $request->input('despachado'))
            ->where('establecimiento_id', $establecimiento_id)
            ->get();

        $aux = [];
        for ($i=0; $i < count($pedidosEst); $i++) { 
            array_push($aux, $pedidosEst[$i]->pedido_id);
        }

        //cargar todos los pedidos en curso (Estado 1, 2, 3)
        $pedidos = \App\Pedido::with('usuario')
            //->with('repartidor')
            ->with(['repartidor.usuario' => function ($query) {
                $query->select('usuarios.id', 'usuarios.nombre', 'usuarios.telefono');
            }])
            ->with('productos.establecimiento')
            ->with('calificacion')
            ->where('estado_pago','aprobado')
            ->whereIn('id', $aux)
            ->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->get();

        if(count($pedidosEst) == 0 && count($pedidos) == 0){

            if ($request->input('despachado') == 1) {
                $msgError = 'No existen pedidos en curso en los últimos 30 días.';
            }else if ($request->input('despachado') == 2) {
                $msgError = 'No existen pedidos finalizados en los últimos 30 días.';
            }

            return response()->json(['error'=>$msgError], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    /*Retorna los ultimos 10 pedidos por despachar*/
    public function pedidosPorDespachar($establecimiento_id)
    {
        $pedidosEst = \App\Ruta::select('id', 'pedido_id')
            ->where('despachado', 1)
            ->where('establecimiento_id', $establecimiento_id)
            /*->whereHas('pedido', function ($query) {
                    $query->where('estado_pago','aprobado');
                })*/
            //->orderBy('id', 'desc')
            //->take(10)
            ->get();

        $aux = [];
        for ($i=0; $i < count($pedidosEst); $i++) { 
            array_push($aux, $pedidosEst[$i]->pedido_id);
        }

        //pedidos en curso no despachados por el establecimiento
        $enCurso = \App\Pedido::select('id', 'estado', 'usuario_id', 'repartidor_id', 'estado_pago', 'created_at')
            ->with(['usuario' => function ($query) {
                $query->select('id', 'nombre');
            }])
            ->where('estado_pago','aprobado')
            ->whereIn('id', $aux)
            ->where(DB::raw("PERIOD_DIFF(DATE_FORMAT(now(), '%y%m') ,DATE_FORMAT(created_at, '%y%m'))"), '<=', 1)
            ->orderBy('id', 'desc')
            ->take(10)
            ->get();


        return response()->json(['enCurso'=>$enCurso], 200);
         
    }

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

}
