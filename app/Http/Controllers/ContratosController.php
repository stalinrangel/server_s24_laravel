<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ContratosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todas las calificaciones
        $Contratos = \App\Contratos::all();

        if(count($Contratos) == 0){
            return response()->json(['error'=>'No existen Contratos.'], 404);          
        }else{
            return response()->json(['Contratos'=>$Contratos], 200);
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

        $order   = array("\r\n", "\n", "\r", " ", "&");
        $replace = array('%20', '%20', '%20', '%20', '%26');
        $nombre= str_replace($order, $replace, $request->input('nombre'));
        $ci= str_replace($order, $replace, $request->input('ci'));
        $telefono= str_replace($order, $replace, $request->input('telefono'));
        $direccion= str_replace($order, $replace, $request->input('direccion'));
        
        $plan= str_replace($order, $replace, $request->input('plan'));
        $usuario_id= str_replace($order, $replace, $request->input('usuario_id'));

        /*return response()->json([
             'nombre'=>$nombre,
             'ci'=>$ci,
             'telefono'=>$telefono,'direccion'=>$direccion,'firma'=>$firma,'plan'=>$plan,'usuario_id'=>$usuario_id], 200);*/
        /*$nombre= 1;
        $ci= 2;
        $telefono= 3;
        $direccion= 4;
        $firma= 5;
        $plan= 6;
        $usuario_id= 7;*/
        $fi = \App\Repartidor::select('firma')->where('usuario_id',$usuario_id)->orderBy('id', 'desc')->first();


        $firma= $fi->firma;
        
        if ($request->input('pais_id')=='1') {
            if ($firma!=null) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crear.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan."&usuario_id=".$usuario_id);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crear2.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan);
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
        }

        if ($request->input('pais_id')=='2') {
            if ($firma!=null) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crearp.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan."&usuario_id=".$usuario_id);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crear2p.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan);
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
        }

        if ($request->input('pais_id')=='3') {
            if ($firma!=null) {
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crear.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan."&usuario_id=".$usuario_id);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
                    'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                ///curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                $response = curl_exec($ch);
                curl_close($ch);
            }else{
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/contratos/crear2.php?nombre=".$nombre."&ci=".$ci."&telefono=".$telefono."&direccion=".$direccion."&plan=".$plan);
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
        }
            
         


        $Contratos = new \App\Contratos;
        $Contratos->url = 'https://service24.app/apii/public/contratos/contratos/'.$response.'.html';
        $Contratos->usuario_id = $request->input('usuario_id');
        
        if($Contratos->save()){
        //Calificar el pedido
       // if($Contratos=\App\Contratos::create($request->all())){
           return response()->json(['message'=>'Contratos con éxito.',
             'Contratos'=>$Contratos], 200);
        }else{
            return response()->json(['error'=>'Error al crear Contratos.'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    
    public function show_firma($id){

        $Contratos = \App\Repartidor::select('firma')->where('usuario_id',$id)->orderBy('id', 'desc')->first();

        if (count($Contratos)>0) {
            return $Contratos->firma;
        }else{
            return 0;
        }
 
    }
    public function show($id)
    {
        //cargar una Contratos
        $Contratos = \App\Repartidor::select('firma','plan')->where('usuario_id',$id)->orderBy('id', 'desc')->first();
        $contrato = \App\Contratos::where('usuario_id',$id)->orderBy('id', 'desc')->first();
        $Contratos->url = $contrato->url;
        if(count($Contratos)==0){
            return response()->json(['Contratos'=>$null], 200);          
        }else{
            return response()->json(['Contratos'=>$Contratos,'exito'=>1], 200);
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
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Comprobamos si el pedido que nos están pasando existe o no.
        $calificacion=\App\Contratos::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la calificación con id '.$id], 404);          
        }
        
        // Eliminamos la calificacion del pedido.
        $calificacion->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la calificación.'], 200);
    }
}
