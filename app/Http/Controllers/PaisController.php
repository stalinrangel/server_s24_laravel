<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PaisController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //cargar todas las coordenadas
        $coordenadas = \App\Pais::with('ciudad.zonas')->get();

        if(count($coordenadas) == 0){
            return response()->json(['error'=>'No existen coordenadas.'], 404);          
        }else{
            return response()->json(['coordenadas'=>$coordenadas], 200);
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
       // if ( !$request->input('nombre') )
        //{
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
          //  return response()->json(['error'=>'Falta el parámetro nombre.'],422);
        //} 

        

        if($nuevaPais=\App\Pais::create($request->all())){

           return response()->json(['message'=>'Pais creada con éxito.',
             'Pais'=>$nuevaPais], 200);
        }else{
            return response()->json(['error'=>'Error al crear la Pais.'], 500);
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
        //
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
        // Comprobamos si la Pais que nos están pasando existe o no.
        $Pais=\App\Pais::find($id);

        if (count($Pais)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la Pais con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($nombre != null && $nombre!='')
        {
            $Pais->nombre = $nombre;
            $bandera=true;
        }


        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($Pais->save()) {

                return response()->json(['message'=>'Pais editada con éxito.',
                    'Pais'=>$Pais], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la Pais.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la Pais.'],409);
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
        $Pais=\App\Pais::find($id);

        if ($Pais->delete()) {
            return response()->json(['message'=>'Se ha eliminado correctamente el blog.'], 200);
        }else{
            return response()->json(['message'=>'Se no se pudo eliminar la Pais.'], 409);
        }

        
    }
}
