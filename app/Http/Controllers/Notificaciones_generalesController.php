<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class Notificaciones_generalesController extends Controller
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
        //$zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todas las Notificaciones_generales
        $Notificaciones_generales = \App\Notificaciones_generales::where('usuario_id',1)->where('ciudad_id',$request->input('ciudad_id'))->orderBy('id', 'DESC')->get();

        if(count($Notificaciones_generales) == 0){
            return response()->json(['error'=>'No existen Notificaciones_generales.'], 404);          
        }else{
            return response()->json(['Notificaciones_generales'=>$Notificaciones_generales], 200);
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
        //return 1;
        
       
        //Calificar el pedido
        if($Notificaciones_generales=\App\Notificaciones_generales::create($request->all())){

            


           return response()->json(['message'=>'Notificacion registrada con éxito.',
             'categoria'=>$Notificaciones_generales], 200);
        }else{
            return response()->json(['error'=>'Error al registrar la Notificacion.'], 500);
        }

    }

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
    public function enviarNotificacionClientes($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
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

    function compararFechas($a, $b) {
        
        return strtotime($a->created_at) - strtotime($b->created_at);
    }
    public function show2(Request $request)
    {
        $usuarios=[1,$request->input('usuario_id')];
        //cargar una Notificaciones_generales
         $Notificaciones_generales = \App\Notificaciones_generales::whereIn('usuario_id',$usuarios)->where('ciudad_id',$request->input('ciudad_id'))->where('tipo_usuario',2)->orderBy('id', 'DESC')->take(20)->get();
         $Notificaciones = \App\Notificacion::where('usuario_id',$request->input('usuario_id'))->orderBy('id', 'DESC')->take(40)->get();
         $combined=[];
         //return $Notificaciones_generales[0];
         for ($i=0; $i < count($Notificaciones_generales); $i++) { 
            array_push($combined,$Notificaciones_generales[$i]);
         }
         for ($i=0; $i < count($Notificaciones); $i++) { 
            array_push($combined,$Notificaciones[$i]);
         }
         usort($combined, 'compararFechas');
        if(count($combined) == 0){
            return response()->json(['error'=>'No existen Notificaciones_generales.'], 404);          
        }else{
            return response()->json(['Notificaciones_generales'=>$combined], 200);
        }  
    }
    public function show3(Request $request)
    {
        $usuarios=[1,$request->input('usuario_id')];
        //cargar una Notificaciones_generales
         $Notificaciones_generales = \App\Notificaciones_generales::whereIn('usuario_id',$usuarios)/*->where('ciudad_id',$request->input('ciudad_id'))*/->where('tipo_usuario',3)->orderBy('id', 'DESC')->take(20)->get();
         $Notificaciones = \App\Notificacion::where('usuario_id',$request->input('usuario_id'))->orderBy('id', 'DESC')->take(40)->get();
         $combined=[];
         //return $Notificaciones_generales[0];
         for ($i=0; $i < count($Notificaciones_generales); $i++) { 
            array_push($combined,$Notificaciones_generales[$i]);
         }
         for ($i=0; $i < count($Notificaciones); $i++) { 
            array_push($combined,$Notificaciones[$i]);
         }
         usort($combined, 'compararFechas');
        if(count($combined) ==0){
            return response()->json(['error'=>'No existen Notificaciones_generales.'], 404);          
        }else{
            return response()->json(['Notificaciones_generales'=>$combined], 200);
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
        // Comprobamos si la Notificaciones_generales que nos están pasando existe o no.
        $Notificaciones_generales = \App\Notificaciones_generales::find($id);
        
        if(count($Notificaciones_generales)==0){
            return response()->json(['error'=>'No existe la Notificacion con id '.$id], 404);          
        }

        // Listado de campos recibidos teóricamente.
        $visto=$request->input('visto');
        $Notificaciones_generales->visto=$visto;

        
       
            // Almacenamos en la base de datos el registro.
            if ($Notificaciones_generales->update()) {
                return response()->json(['message'=>'Notificaciones_generales editada con éxito.',
                    'Notificaciones_generales'=>$Notificaciones_generales], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la calificación.'], 500);
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
        $Notificaciones_generales=\App\Notificaciones_generales::find($id);

        if(count($Notificaciones_generales)==0){
            return response()->json(['error'=>'No existe la Notificaciones_generales con id '.$id], 404);          
        }
        
        // Eliminamos la Notificaciones_generales del pedido.
        $Notificaciones_generales->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la Notificaciones_generales.'], 200);
    }
}
