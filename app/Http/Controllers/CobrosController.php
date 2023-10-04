<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Hash;
use DB;
use DateTime;
use DateInterval;

class CobrosController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores
        $repartidores=\App\Cobros::where('estado',0)->
        //$repartidores = \App\Repartidor::
            with(['usuario' => function ($query) use ($zonas){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'zona_id', 'token_notificacion')
                    ->with('registro')
                    ->whereIn('zona_id',$zonas);
                }])->with('establecimiento')->orderBy('created_at', 'DESC')
            ->get();

           // return 1;


        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }
    public function index2(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los repartidores
        $repartidores=\App\Cobros::where('estado',1)->
        //$repartidores = \App\Repartidor::
            with(['usuario' => function ($query) use ($zonas){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'zona_id', 'token_notificacion')
                    ->with('registro')
                    ->whereIn('zona_id',$zonas);
                }])->with('establecimiento')->orderBy('created_at', 'DESC')
            ->get();

           // return 1;


        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }

    public function pago(Request $request, $id)
    {
        //cargar todos los repartidores
        $Cobros=\App\Cobros::where('id',$id)->with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion');
                }])->first();

        $Cobros->monto=$request->input('monto'); 
       // $Cobros->fecha_pago=new DateTime();
        $Cobros->prox_pago = new DateTime();
        $Cobros->establecimiento_id = $request->input('establecimiento_id');
        $Cobros->usuario_id = $request->input('usuario_id');
        $Cobros->observacion = $request->input('observacion');
        $Cobros->estado=1;
        
        if(count($Cobros) == 0){
            return response()->json(['error'=>'No existen Cobros.'], 404);          
        }else{
        	if ($Cobros->save()) {
        		$newstr="Tu pago ha sido procesado.";
        		$this->enviarNotificacion($Cobros->usuario->token_notificacion, $newstr, 'null', 9, $obj);
        		/*$Cobros = new \App\Cobros;
		        $Cobros->monto = 100;
		        $Cobros->estado = 0;
		        $Cobros->fecha_pago = new DateTime();
                $date = new DateTime();
                $date->add(new DateInterval('P1D'));
		        $Cobros->prox_pago = $date;
		        $Cobros->establecimiento_id = $request->input('establecimiento_id');
		        $Cobros->usuario_id = $request->input('usuario_id');
                $Cobros->observacion='Nuevo recibo de la fecha '.$date->format('Y-m-d');

		        if($Cobros->save()){
        		  return response()->json(['Cobros'=>$Cobros], 200);
                }else{*/
                  return response()->json(['error'=>'No se actualizó el cobro.'], 404);  
                //}
        	}
        } 

           $this->enviarNotificacion($usuario->token_notificacion, $newstr, 'null', 9, $obj);

    }

    public function enviarNotificacion($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        ///return 1;
                    $msg=$msg;
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    // Procesa primero \r\n así no es convertido dos veces.
                    $newstr = str_replace($order, $replace, $msg);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/alinstanteAPI/public/onesignal.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        return $response = curl_exec($ch);
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
        /*if ( !$request->input('email') || !$request->input('password') ||
            !$request->input('nombre') || !$request->input('telefono') ||
            !$request->input('ciudad') || !$request->input('estado') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } */
        
        
        
        /*Primero creo una instancia en la tabla Cobross*/
        $Cobros = new \App\Cobros;
        $Cobros->monto = $request->input('monto');
        $Cobros->estado = $request->input('estado');
        $Cobros->fecha_pago = $request->input('fecha_pago');
        $Cobros->prox_pago = $request->input('prox_pago');
        $Cobros->establecimiento_id = $request->input('establecimiento_id');
        $Cobros->usuario_id = $request->input('usuario_id');
        $Cobros->observacion = $request->input('observacion');

        if($Cobros->save()){

           return response()->json(['message'=>'Cobros creado con éxito.', 'user_Cobros'=>$Cobros], 200);
        }else{
            return response()->json(['error'=>'Error al crear el Cobros.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cobros($id)
    {
        //cargar un Cobros
        $Cobros = \App\Cobros::where('usuario_id', $id)->
        //$repartidores = \App\Repartidor::
            with(['usuario' => function ($query) {
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'zona_id', 'token_notificacion')
                    ->with('registro');
                }])->with('establecimiento')->orderBy('created_at', 'DESC')
            ->get();


        $por_pagar=[];
        $pagados=[];

        for ($i=0; $i < count($Cobros); $i++) { 
            if ($Cobros[$i]->estado==0) {
                array_push($por_pagar,$Cobros[$i]);
            }
            if ($Cobros[$i]->estado==1) {
                array_push($pagados,$Cobros[$i]);
            }
        }
        if(count($Cobros)==0){
            return response()->json(['error'=>'No existe el Cobros con id '.$id], 404);          
        }else{

            return response()->json(['por_pagar'=>$por_pagar,'pagados'=>$pagados], 200);
        }
    }

    public function pagados($id)
    {
        //cargar un Cobros
        $Cobros = \App\Cobros::where('estado',1)->where('usuario_id', $id)->
        //$repartidores = \App\Repartidor::
            with(['usuario' => function ($query) {
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'zona_id', 'token_notificacion')
                    ->with('registro');
                }])->with('establecimiento')->orderBy('created_at', 'DESC')
            ->get();

        if(count($Cobros)==0){
            return response()->json(['error'=>'No existe el Cobros con id '.$id], 404);          
        }else{

            return response()->json(['Cobros'=>$Cobros], 200);
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
        // Comprobamos si el Cobros que nos están pasando existe o no.
        $Cobros = \App\Cobros::find($id);

        if (count($Cobros)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el Cobros con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $monto=$request->input('monto'); 
        $estado=$request->input('estado');  
        $fecha_pago=$request->input('fecha_pago');
        $prox_pago = $request->input('prox_pago');
        $establecimiento_id = $request->input('establecimiento_id');
        $usuario_id = $request->input('usuario_id');
        
        $bandera = false;

        
        if ($monto != null && $monto!='')
        {
            $Cobros->monto = $monto;
            $bandera=true;
        }

        if ($estado != null && $estado!='')
        {
            $Cobros->estado = $estado;
            $bandera=true;
        }

        if ($fecha_pago != null && $fecha_pago!='')
        {
            $Cobros->fecha_pago = $fecha_pago;
            $bandera=true;
        }

        if ($prox_pago != null && $prox_pago!='')
        {
            $Cobros->prox_pago = $prox_pago;
            $bandera=true;
        }

        if ($establecimiento_id != null && $establecimiento_id!='')
        {
            $Cobros->establecimiento_id = $establecimiento_id;
            $bandera=true;
        }

        if ($usuario_id != null && $usuario_id!='')
        {
            $Cobros->usuario_id = $usuario_id;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($Cobros->save()) {

                return response()->json(['message'=>'Cobros actualizado con éxito.', 'Cobros'=>$Cobros], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el Cobros.'], 500);
            }
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del Cobros.'],409);
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
        // Comprobamos si el Cobros que nos están pasando existe o no.
        $Cobros=\App\Cobros::find($id);

        if (count($Cobros)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el Cobros con id '.$id], 404);
        }

        $pedidos = $Cobros->pedidos;

        for ($i=0; $i < count($pedidos) ; $i++) {
            if ($pedidos[$i]->estado == 2 || $pedidos[$i]->estado == 3) {
                return response()->json(['error'=>'No se puede eliminar el Cobros porque posee pedidos en curso.'], 409);
             } 
        }

        for ($i=0; $i < count($pedidos) ; $i++) { 
            $pedidos[$i]->Cobros_id = null;
            $pedidos[$i]->save();
        }

        $Cobros=\App\User::find($Cobros->Cobros_id);

        //Eliminamos el chat si lo tiene
        $chat = $Cobros->chat_Cobros;
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

        // Eliminamos el Cobros.
        $Cobros->delete();

        // Eliminamos el Cobros del Cobros.
        $Cobros->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el Cobros.'], 200);
    }

    public function setPosicion(Request $request, $id)
    {
        // Comprobamos si el Cobros que nos están pasando existe o no.
        $Cobros = \App\Cobros::find($id);

        if (count($Cobros)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el Cobros con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $lat=$request->input('lat');
        $lng=$request->input('lng');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($lat != null && $lat!='')
        {
            $Cobros->lat = $lat;
            $bandera=true;
        }

        if ($lng != null && $lng!='')
        {
            $Cobros->lng = $lng;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($Cobros->save()) {
                return response()->json(['message'=>'ok.'], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el Cobros.'], 500);
            }
        }
        else
        {
            // Se devuelve un array error con los error encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato del Cobros.'],409);
        }
    }

    public function repDisponibles()
    {
        //cargar todos los Cobros en ON, Trabajando y Disponibles
        $Cobros = \App\Cobros::with('Cobros')
                ->where('estado', 'ON')
                ->where('activo', 1)
                ->where('ocupado', 2)
                ->get();

        if(count($Cobros) == 0){
            return response()->json(['error'=>'No hay Cobros disponibles.'], 404);          
        }else{
            return response()->json(['Cobros'=>$Cobros], 200);
        } 
    }

    public function miPedidoEnEspera($Cobros_id)
    {
        //cargar todos los pedidos en curso (Estado 2, 3)
        $pedido = \App\Pedido::with('Cobros')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            ->where('Cobros_id', $Cobros_id)
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

    /*Retorna el pedido en curso de un Cobros_id*/
    public function miPedidoEnCurso($Cobros_id)
    {
        //cargar todos los pedidos en curso (Estado 2, 3)
        $pedido = \App\Pedido::with('Cobros')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            ->where('Cobros_id', $Cobros_id)
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
    Cobros_id filtrados por fecha*/
    public function historialPedidos(Request $request, $Cobros_id)
    {
        //cargar todos los pedidos
        $pedidos = \App\Pedido::with('Cobros')
            ->with('productos.subcategoria.categoria')
            ->with('ruta')
            //->with('calificacion')
            ->where('Cobros_id', $Cobros_id)
            //->where(DB::raw('DAY(created_at)'),1)
            ->where('estado',4)
            //->where(DB::raw('MONTH(created_at)'),$request->input('mes'))
            //->where(DB::raw('YEAR(created_at)'),$request->input('anio'))
            ->get();

        for ($i=0; $i < count($pedidos); $i++) {
            $calificacion=\App\Calificacion::where('pedido_id',$pedidos[$i]->id)/*->where('califique_a',$pedidos[$i]->Cobros->id)*/->with('Cobros')->with('producto')->get();
            $pedidos[$i]->calificacion=$calificacion;
        }

        if(count($pedidos) == 0){
            return response()->json(['error'=>'No tienes pedidos registrados en la fecha '.$request->input('mes').'/'.$request->input('anio')], 404);          
        }else{
            return response()->json(['pedidos'=>$pedidos], 200);
        } 
    }

    /*Retorna el conteo de pedidos en curso 
    y finalizados de un Cobros_id*/
    public function conteoPedidos($Cobros_id)
    {
        //contar todos los pedidos en curso (Estado 1 2 3)
        $enCurso = \App\Pedido::
            where('Cobros_id',$Cobros_id)
            ->where(function ($query) {
                $query
                    ->where('estado',1)
                    ->orWhere('estado',2)
                    ->orWhere('estado',3);
            })
            ->count();

        //contar todos los pedidos en finalizados (Estado 4)
        $enFinalizados = \App\Pedido::
            where('Cobros_id',$Cobros_id)
            ->where('estado',4)
            ->count();

        return response()->json(['enCurso'=>$enCurso, 'enFinalizados'=>$enFinalizados], 200);
         
    }

}
