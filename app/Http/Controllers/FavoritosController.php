<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class FavoritosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todas las calificaciones
        $Favoritos = \App\Favoritos::all();

        if(count($Favoritos) == 0){
            return response()->json(['error'=>'No existen Favoritos.'], 404);          
        }else{
            return response()->json(['Favoritos'=>$Favoritos], 200);
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
       
        $AllFavorito=\App\Favoritos::all();

        $usuario_id= $request->input('usuario_id');
        $productos_id= $request->input('productos_id');
        $establecimiento_id= $request->input('establecimiento_id');
        $band=0;
        for ($i=0; $i < count($AllFavorito); $i++) { 
            if ($AllFavorito[$i]->usuario_id==$usuario_id && $establecimiento_id==$AllFavorito[$i]->establecimiento_id && $productos_id==$AllFavorito[$i]->productos_id) {
                $band=1;
            }
        }

        if ($band==0) {

            if($Favoritos=\App\Favoritos::create($request->all())){
            return response()->json(['message'=>'Favoritos con éxito.',
                'Favoritos'=>$Favoritos], 200);
            }else{
                return response()->json(['error'=>'Error al crear Favoritos.'], 500);
            }
        }
        return response()->json(['error'=>'Error al crear Favoritos.'], 500);

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

        $Favoritos = \App\Favoritos::where('usuario_id',$usuario_id)->where('establecimiento_id',$producto_id)->get();

        if (count($Favoritos)>0) {
            return 1;
        }else{
            return 0;
        }
 
    }
    public function show($id)
    {
        //cargar una Favoritos
        $Favoritos = \App\Favoritos::where('usuario_id',$id)->with('productos.subcategoria')->get();

	$a=array();
        
	for ($i=0; $i < count($Favoritos); $i++) { 
	    $Favoritos[$i]->productos[0]->favorito_id=$Favoritos[$i]->id;
    	    array_push($a,$Favoritos[$i]->productos[0]);
   	 }
        if(count($Favoritos)==0){
            return response()->json(['error'=>'No tienes favoritos'], 404);          
        }else{
            return response()->json(['Favoritos'=>$a], 200);
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
        $calificacion=\App\Favoritos::find($id);

        if(count($calificacion)==0){
            return response()->json(['error'=>'No existe la calificación con id '.$id], 404);          
        }
        
        // Eliminamos la calificacion del pedido.
        $calificacion->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la calificación.'], 200);
    }
}
