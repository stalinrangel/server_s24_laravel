<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CalificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todas las calificaciones
        $calificaciones = \App\Calificacion::all();

        if(count($calificaciones) == 0){
            return response()->json(['error'=>'No existen calificaciones.'], 404);          
        }else{
            return response()->json(['calificaciones'=>$calificaciones], 200);
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
        if ( !$request->input('pedido_id') ||
             !$request->input('puntaje'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 
        
        $pedido = \App\Pedido::find($request->input('pedido_id'));
        if(count($pedido) == 0){
           // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'No existe el pedido que se quiere calificar.'], 409);
        }

        $calificacion=\App\Calificacion::where('pedido_id',$request->input('pedido_id'))->get();
        $obj=$pedido->id;
        if(count($calificacion) == 0 || count($calificacion) == 1){
            if ($request->input('califico')==3) {
                $ya_califico_p=0;
                for ($i=0; $i < count($calificacion); $i++) { 
                    if ($calificacion[$i]->tipo_usuario==2) {
                        $ya_califico_p=1;
                    }
                }
                for ($i=0; $i < count($calificacion); $i++) { 
                    if ($calificacion[$i]->tipo_usuario==3) {
                        $ya_califico_p=1;
                    }
                }

                if ($ya_califico_p==0) {
                   $usuario = \App\User::where('id', $pedido->usuario_id)->first();
                 $this->enviarNotificacionCliente($usuario->token_notificacion, 'Califica%20al%20proveedor%20del%20servicio%20S00'.$pedido->id, $pedido->id, 6, $obj);
                }
                
            }

            if ($request->input('califico')==2) {
                $ya_califico_c=0;
                for ($i=0; $i < count($calificacion); $i++) { 
                    if ($calificacion[$i]->tipo_usuario==2) {
                        $ya_califico_c=1;
                    }
                }
                for ($i=0; $i < count($calificacion); $i++) { 
                    if ($calificacion[$i]->tipo_usuario==3) {
                        $ya_califico_c=1;
                    }
                }
                if ($ya_califico_c==0) {
                    $repartidor = \App\Repartidor::where('id', $pedido->repartidor_id)->first();
                    $proveedor = \App\User::where('id', $repartidor->usuario_id)->first();
                    $this->enviarNotificacion($proveedor->token_notificacion, 'Califica%20al%20cliente%20del%20servicio%20S00'.$pedido->id, $pedido->id, 16, $obj);
                }
            } 
        }

        $aux = $pedido->calificacion;

        if (sizeof($aux) > 0 )
        {
            // Devolvemos un código 409 Conflict. 
            //return response()->json(['error'=>'Este pedido ya está calificado.'], 409);
        }

        //Calificar el pedido
        if($calificacion=\App\Calificacion::create($request->all())){

           return response()->json(['message'=>'Pedido calificado con éxito.',
             'categoria'=>$calificacion], 200);
        }else{
            return response()->json(['error'=>'Error al crear la calificación.'], 500);
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
    public function enviarNotificacionCliente($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/alinstanteAPI/public/onesignalclientes.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
            'Authorization: Basic MDNkOGZlNmMtYzlhZC00MWIzLWFlNDktOTQyOGQzMDJhYWU3'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        //$fields = array('contenido'=>$msg);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        //curl_setopt($ch, CURLOPT_POSTFIELDS, "accion=t");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

        $response = curl_exec($ch);
        curl_close($ch);

        //return $response;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //cargar una calificacion
        $calificacion = \App\Calificacion::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la calificación con id '.$id], 404);          
        }else{
            return response()->json(['calificacion'=>$calificacion], 200);
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
        // Comprobamos si la calificacion que nos están pasando existe o no.
        $calificacion = \App\Calificacion::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la calificación con id '.$id], 404);          
        }

        // Listado de campos recibidos teóricamente.
        $puntaje=$request->input('puntaje');
        $comentario=$request->input('comentario');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($puntaje != null && $puntaje!='')
        {
            $calificacion->puntaje = $puntaje;
            $bandera=true;
        }

        if ($comentario != null && $comentario!='')
        {
            $calificacion->comentario = $comentario;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($calificacion->save()) {
                return response()->json(['message'=>'Calificación editada con éxito.',
                    'calificacion'=>$calificacion], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la calificación.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la la calificación.'],409);
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
        $calificacion=\App\Calificacion::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la calificación con id '.$id], 404);          
        }
        
        // Eliminamos la calificacion del pedido.
        $calificacion->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la calificación.'], 200);
    }
}
