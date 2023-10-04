<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class NotificacionesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   //$request->input('ciudad_id')
        //cargar todas las calificaciones
        $Notificacion = \App\Notificacion::where('visto',0)->with(['usuario' => function ($query) use ($request){
                $query->where('ciudad',$request->input('ciudad_id'));
            }])->orderBy('id', 'desc')
            ->get();
        $aux=[];
        for ($i=0; $i < count($Notificacion); $i++) { 
           if ($Notificacion[$i]->usuario!=null) {
                array_push($aux,$Notificacion[$i]);
            } 
        }

        if(count($aux) == 0){
            return response()->json(['notificaciones'=>[]], 200);          
        }else{
            return response()->json(['notificaciones'=>$aux], 200);
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
        
        if($Notificacion=\App\Notificacion::create($request->all())){

           return response()->json(['message'=>'Notificacion con éxito.',
             'Notificacion'=>$Notificacion], 200);
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
        // Comprobamos si la Notificacion que nos están pasando existe o no.
        $Notificacion = \App\Notificacion::find($id);

        if(count($Notificacion)==0){
            return response()->json(['error'=>'No existe la Notificacion con id '.$id], 404);          
        }

        // Listado de campos recibidos teóricamente.
        $visto=$request->input('visto');
        $Notificacion->visto=$visto;
            // Almacenamos en la base de datos el registro.
        if ($Notificacion->update()) {
            return response()->json(['message'=>'Notificacion editada con éxito.',
                'Notificacion'=>$Notificacion], 200);
        }else{
            return response()->json(['error'=>'Error al actualizar la Notificacion.'], 500);
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
