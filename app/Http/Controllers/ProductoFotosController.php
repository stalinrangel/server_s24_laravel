<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProductoFotosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todas las calificaciones
        $Producto_foto = \App\Producto_foto::all();

        if(count($Producto_foto) == 0){
            return response()->json(['error'=>'No existen Producto_foto.'], 404);          
        }else{
            return response()->json(['Producto_foto'=>$Producto_foto], 200);
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
        if($Producto_foto=\App\Producto_foto::create($request->all())){
           return response()->json(['message'=>'Producto_foto con éxito.',
             'Producto_foto'=>$Producto_foto], 200);
        }else{
            return response()->json(['error'=>'Error al crear Producto_foto.'], 500);
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
        //cargar una Producto_foto
        $Producto_foto = \App\Producto_foto::where('id',$id)->get();
        
    
        if(count($Producto_foto)==0){
            return response()->json(['error'=>'No tienes Producto_foto'], 404);          
        }else{
            return response()->json(['Producto_foto'=>$a], 200);
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
        $calificacion=\App\Producto_foto::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la foto con id '.$id], 404);          
        }
        
        // Eliminamos la calificacion del pedido.
        $calificacion->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la foto.'], 200);
    }
}
