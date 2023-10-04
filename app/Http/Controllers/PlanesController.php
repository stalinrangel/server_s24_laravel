<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PlanesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('panel') != null && $request->input('panel')!='') {
            $pais_id=$request->input('pais_id');
            //cargar todas las calificaciones
            $Planes = \App\Planes::with('restricciones')->get();

            if(count($Planes) == 0){
                return response()->json(['error'=>'No existen Planes.'], 404);          
            }else{
                return response()->json(['Planes'=>$Planes], 200);
            } 
        }else {
            $pais_id=$request->input('pais_id');
            //cargar todas las calificaciones
            $Planes = \App\Planes::with('restricciones')->get();

            $planesaux=[];
            //return $request->input('tipo');
            for ($i=0; $i < count($Planes); $i++) { 

                if ($request->input('tipo')=='1') {
                    $tipo='servicios';
                }
                if ($request->input('tipo')=='2') {
                    $tipo='comercios';
                }

                if ($Planes[$i]->tipo_plan!="Opciones" && $Planes[$i]->tipo==$tipo) {
                    array_push($planesaux, $Planes[$i]);
                }
            }
            if(count($planesaux) == 0){
                return response()->json(['error'=>'No existen Planes.'], 404);          
            }else{
                return response()->json(['Planes'=>$planesaux], 200);
            } 
        }
        
    }

    public function index2(Request $request)
    {
        $pais_id=$request->input('pais_id');
        //cargar todas las calificaciones
        $Planes = \App\Planes::where('pais_id',$pais_id)->get();

        $planesaux=[];

        for ($i=0; $i < count($Planes); $i++) { 
            if ($Planes[$i]->tipo_plan!="Opciones") {
                array_push($planesaux, $Planes[$i]);
            }
        }
        if(count($planesaux) == 0){
            return response()->json(['error'=>'No existen Planes.'], 404);          
        }else{
            return response()->json(['Planes'=>$planesaux], 200);
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
       
        //Calificar el pedido
        if($Planes=\App\Planes::create($request->all())){
           return response()->json(['message'=>'Planes con éxito.',
             'Planes'=>$Planes], 200);
        }else{
            return response()->json(['error'=>'Error al crear Planes.'], 500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    
    public function check(Request $request){

        $usuario_id=$request->input('usuario_id');
        $producto_id=$request->input('producto_id');

        $Planes = \App\Planes::where('usuario_id',$usuario_id)->where('establecimiento_id',$producto_id)->get();

        if (count($Planes)>0) {
            return 1;
        }else{
            return 0;
        }
 
    }
    public function show($id)
    {
        //cargar una Planes
        $Planes = \App\Planes::where('id',$id)->get();
        
	
        if(count($Planes)==0){
            return response()->json(['error'=>'No tienes Planes'], 404);          
        }else{
            return response()->json(['Planes'=>$a], 200);
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
        $Planes = \App\Planes::find($id);

        if(count($Planes)==0){
            return response()->json(['error'=>'No existe Planes con id '.$id], 404);          
        }

        // Listado de campos recibidos teóricamente.
        $descripcion=$request->input('descripcion');
        $tipo_plan=$request->input('tipo_plan');
        $costo=$request->input('costo');
        $descuento=$request->input('descuento');
        $recomendado=$request->input('recomendado');
        $tipo=$request->input('tipo');
        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($tipo_plan != null && $tipo_plan!='')
        {
            $Planes->tipo_plan = $tipo_plan;
            $bandera=true;
        }
        if ($descripcion != null && $descripcion!='')
        {
            $Planes->descripcion = $descripcion;
            $bandera=true;
        }
        if ($costo != null && $costo!='')
        {
            $Planes->costo = $costo;
            $bandera=true;
        }
        if ($descuento != null && $descuento!='')
        {
            $Planes->descuento = $descuento;
            $bandera=true;
        }
        if (true)
        {
            $Planes->recomendado = $recomendado;
            $bandera=true;
        }
        if ($tipo != null && $tipo!='')
        {
            $Planes->tipo = $tipo;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($Planes->save()) {
                return response()->json(['message'=>'Planes editada con éxito.',
                    'Planes'=>$Planes], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la Planes.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la la Planes.'],409);
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
        $calificacion=\App\Planes::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la plan con id '.$id], 404);          
        }
        
        // Eliminamos la calificacion del pedido.
        $calificacion->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la calificación.'], 200);
    }
}
