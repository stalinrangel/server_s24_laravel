<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class SubCategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $subcategorias = \App\Subcategoria::where('ciudad_id',$request->input('ciudad_id'))->with('ciudad.pais')->with('categoria')->with('productos.establecimiento')->get();
        }else{
            $subcategorias = \App\Subcategoria::with('ciudad.pais')->with('categoria')->with('productos.establecimiento')->get();
        }
        

        if(count($subcategorias) == 0){
            return response()->json(['error'=>'No existen subcategorías.'], 404);          
        }else{
            return response()->json(['subcategorias'=>$subcategorias], 200);
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
        // Listado de campos recibidos teóricamente.
        //$nombre=$request->input('nombre'); 

        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('nombre') ||
             !$request->input('estado') ||
             !$request->input('categoria_id'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 
        
        $aux = \App\Subcategoria::where('nombre', $request->input('nombre'))->get();
        if(count($aux)!=0){
           // Devolvemos un código 409 Conflict. 
           // return response()->json(['error'=>'Ya existe una subcategoría con ese nombre.'], 409);
        }

        $categoria = \App\Categoria::where('id',$request->input('categoria_id'))->get();
        if(count($categoria)==0){
           // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'No existe la categoría con id '.$request->input('categoria_id')], 409);
        }

        if($nuevaSubCategoria=\App\Subcategoria::create($request->all())){
           return response()->json(['message'=>'Subcategoría creada con éxito.',
             'subcategoria'=>$nuevaSubCategoria], 200);
        }else{
            return response()->json(['error'=>'Error al crear la subcategoría.'], 500);
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
        //cargar una subcat
        $subcategoria = \App\Subcategoria::find($id);

        if(count($subcategoria)==0){
            return response()->json(['error'=>'No existe la subcategoría con id '.$id], 404);          
        }else{
            return response()->json(['subcategoria'=>$subcategoria], 200);
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
        // Comprobamos si la subcategoria que nos están pasando existe o no.
        $subcategoria=\App\Subcategoria::find($id);

        if (count($subcategoria)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la subcategoría con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $ingles=$request->input('ingles');
        $imagen=$request->input('imagen');
        $categoria_id=$request->input('categoria_id');
        $ciudad_id=$request->input('ciudad_id');
        $estado=$request->input('estado');
        $productos=$request->input('productos');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($nombre != null && $nombre!='')
        {
            $aux = \App\Subcategoria::where('nombre', $request->input('nombre'))
            ->where('id', '<>', $subcategoria->id)->get();

            if(count($aux)!=0){
               // Devolvemos un código 409 Conflict. 
               // return response()->json(['error'=>'Ya existe otra subcategoría con ese nombre.'], 409);
            }

            $subcategoria->nombre = $nombre;
            $bandera=true;
        }

        if ($ingles != null && $ingles!='')
        {
            $subcategoria->ingles = $ingles;
            $bandera=true;
        }

        if ($ciudad_id != null && $ciudad_id!='')
        {
            $categoria->ciudad_id = $ciudad_id;
            $bandera=true;
        }

        if ($imagen != null && $imagen!='')
        {
            $subcategoria->imagen = $imagen;
            $bandera=true;
        }

        if ($categoria_id != null && $categoria_id!='')
        {
            // Comprobamos si la categoria que nos están pasando existe o no.
            $categoria = \App\Categoria::find($categoria_id);

            if(count($categoria)==0){
                return response()->json(['error'=>'No existe la categoría con id '.$categoria_id], 404);          
            } 

            if ($subcategoria->categoria_id != $categoria_id) {
                //Comprobar que no exista una subcat con el mismo nombre en la nueva categoria
                $aux2 = \App\Subcategoria::where('nombre', $subcategoria->nombre)
                ->where('categoria_id', $categoria_id)->get();

                if(count($aux2)!=0){
                   // Devolvemos un código 409 Conflict. 
                    return response()->json(['error'=>'Ya existe una subcategoria con el nombre '.$subcategoria->nombre.' asociada a la categoría '.$categoria->nombre.'.'], 409);
                }
            }

            $subcategoria->categoria_id = $categoria_id;
            $bandera=true;
        }

        if ($estado != null && $estado!='')
        {

            if ($estado == 'OFF') {
                $productos = $subcategoria->productos;

                if (sizeof($productos) > 0)
                {
                    for ($i=0; $i < count($productos) ; $i++) { 
                        $productos[$i]->estado = $estado;
                        $productos[$i]->save();
                    }
                }
            }

            $subcategoria->estado = $estado;
            $bandera=true;
        }

        if (sizeof($productos) > 0 )
        {
            $bandera=true;

            $productos = json_decode($productos);
            for ($i=0; $i < count($productos) ; $i++) {

                if ($productos[$i]->estado == 'ON') {

                    $producto = \App\Producto::find($productos[$i]->id);

                    if(count($producto) == 0){
                       // Devolvemos un código 409 Conflict. 
                        return response()->json(['error'=>'No existe el producto con id '.$productos[$i]->id], 409);
                    }else{
                        $producto->estado = $productos[$i]->estado;
                        $producto->save();
                    }
                }  
            }
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($subcategoria->save()) {
                return response()->json(['message'=>'Subcategoría editada con éxito.',
                    'subcategoria'=>$subcategoria], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la subcategoría.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la subcategoría.'],409);
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
        // Comprobamos si la subcategoria existe o no.
        $subcategoria=\App\Subcategoria::find($id);

        if (count($subcategoria)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la subcategoría con id '.$id], 404);
        }
       
        $productos = $subcategoria->productos;

        if (sizeof($productos) > 0)
        {
            // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'Esta subcategoría no puede ser eliminada porque posee productos asociados.'], 409);
        }

        // Eliminamos la subcategoria si no tiene relaciones.
        $subcategoria->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la subcategoría.'], 200);
    }

    public function subcatsProdsEst()
    {
        //cargar todas las subcategorias con sus productos y su establecimiento
        $subcategorias = \App\Subcategoria::with('productos.establecimiento')->get();

        if(count($subcategorias) == 0){
            return response()->json(['error'=>'No existen subcategorías.'], 404);          
        }else{
            return response()->json(['subcategorias'=>$subcategorias], 200);
        } 
    }

    public function subcategoriasCategoria()
    {
        //cargar todos las subcategorias con su categoria
        $subcategorias = \App\Subcategoria::with('categoria')->with('productos.establecimiento')->get();

        if(count($subcategorias) == 0){
            return response()->json(['error'=>'No existen subcategorías.'], 404);          
        }else{
            return response()->json(['subcategorias'=>$subcategorias], 200);
        } 
        
    }

    /*Retorna productos de la subcategoria.
    donde el estable al que pertenece el producto esta ON*/
    public function subcategoriaProductos(Request $request, $id)
    {
        //cargar una subcat con sus subcat
       //$subcategoria = \App\Subcategoria::where('id', $id)->where('estado', 'ON')->with('productos.establecimiento.usuario.repartidor.registro')->get();

        //$subcategoria = \App\Subcategoria::with('productos.establecimiento.usuario.repartidor.registro')->find($id);
        $zona_id=$request->input('zona_id');
        $zona_id=(int)$zona_id;
        //return $zona_id;
        if (!$request->input('zona_id')){
           $subcategoria = \App\Subcategoria::with(['productos' => function ($query){
                    $query->where('productos.estado', 'ON')
                    ->with('establecimiento.usuario.repartidor.registro');
               }])->find($id);
        }

        if ( $request->input('zona_id')!= null && $request->input('zona_id')!='' && $request->input('zona_id')!=0) {

            if ($zona_id==1000) {
               return response()->json(['productos'=>[]], 200);
            }
            if ($zona_id!=1000) {
                //return $zona_id;
                $subcategoria = \App\Subcategoria::with(['productos' => function ($query)use ($zona_id){
                    $query
                    ->where('productos.estado', 'ON')
                    //->where('productos.zona_id',$zona_id)
                    ->with('establecimiento.usuario.repartidor.registro')
                    ->whereHas('zonas2', function ($query) use ($zona_id) {
                        $query->where('zonas_productos.zonas_id', $zona_id);
                    });
               }])->find($id);
               // return response()->json(['subcategoria'=>$subcategoria], 200);
            }
           
       }

       
       

      /* $subcategoria = \App\Subcategoria::with(['productos' => function ($query){
                $query->where('productos.estado', 'ON')
                    ->with('establecimiento.usuario.repartidor.registro');
            }])->find($id);*/

        if(count($subcategoria)==0){
            return response()->json(['error'=>'No existe la subcategoría con id '.$id], 404);          
        }else{

            $aux = [];

            for ($i=0; $i < count($subcategoria->productos) ; $i++) { 
                //if ($subcategoria->productos[$i]->estado == 'ON') {
                   // if ($subcategoria->productos[$i]->establecimiento->estado == 'ON') {
                       // if ($subcategoria->productos[$i]->establecimiento->usuario->repartidor->estado == 'ON' && $subcategoria->productos[$i]->establecimiento->usuario->repartidor->activo == '1') {
                            array_push($aux, $subcategoria->productos[$i]);
                        //}
                   // }
                //}
            }
            for ($i=0; $i < count($aux) ; $i++) { 
                $calificaciones = \App\Calificacion::where('producto_id',$aux[$i]->id)->with('usuario')->with('producto')->get();
                $servicios = \App\Producto::where('establecimiento_id',$aux[$i]->establecimiento->id)->with('subcategoria')->get();
                //return $calificaciones;
                $calif=[];
                for ($k=0; $k < count($calificaciones); $k++) { 
                    if ($calificaciones[$k]->usuario!=null && $calificaciones[$k]->tipo_usuario!=3) {
                        array_push($calif,$calificaciones[$k]);
                    }
                }
                $calificaciones=$calif;
                $calif=[];
                if (count($calificaciones)>0) {
                    $promedio=0;
                    for ($j=0; $j < count($calificaciones); $j++) { 
                        $promedio+=$calificaciones[$j]->puntaje;
                    }
                    $aux[$i]->promedio_calificacion=round($promedio/count($calificaciones), 0, PHP_ROUND_HALF_UP); 
                    $aux[$i]->calificaciones=$calificaciones;
                    $aux[$i]->servicios=$servicios;
                }else{
                  $aux[$i]->promedio_calificacion=0;  
                  $aux[$i]->calificaciones=$calificaciones;
                  $aux[$i]->servicios=$servicios;
                }

                $img = \App\Calificacion::select('imagen')->where('producto_id',$aux[$i]->id)->whereNotNull('imagen')->get();
                $aux[$i]->fotos2=$img;
            }
           
            //return $promedio;
            return response()->json(['productos'=>$aux], 200);
        } 
    }

    //Usada en el panel
    public function subcategoriasHabilitadas(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $subcategorias = \App\Subcategoria::where('ciudad_id',$request->input('ciudad_id'))->with('ciudad.pais')->with('categoria')->with('productos.establecimiento')->where('estado', 'ON')->get();
        }else{
            $subcategorias = \App\Subcategoria::with('ciudad.pais')->with('categoria')->with('productos.establecimiento')->where('estado', 'ON')->get();
        }
        //cargar todas las subcat en estado ON
       // $subcategorias = \App\Subcategoria::with('productos.establecimiento')->where('estado', 'ON')->get();

        if(count($subcategorias) == 0){
            return response()->json(['error'=>'No existen subcategorías habilitadas.'], 404);          
        }else{
            return response()->json(['subcategorias'=>$subcategorias], 200);
        }   
    }

    //Usada en el panel
    public function subcatHabCat($categoria_id)
    {
        //cargar todas las subcat en estado ON
        $subcategorias = \App\Subcategoria::with('productos.establecimiento')->where('categoria_id', $categoria_id)
            ->where('estado', 'ON')->get();

        if(count($subcategorias) == 0){
            return response()->json(['error'=>'No existen subcategorías habilitadas para esta categoría.'], 404);          
        }else{
            return response()->json(['subcategorias'=>$subcategorias], 200);
        }   
    }

    /*Usada para llenar las subcategorias de ciudad de panamá*/
    public function setTablaSubCategorias()
    {
        //cargar  
        $subcategorias = \App\Subcategoria2::where('ciudad_id', 29)->get();

        for ($i=0; $i < count($subcategorias); $i++) {

            $aux = \App\Categoria2::where('id', $subcategorias[$i]->categoria_id)
                ->get();

            $aux2 = \App\Categoria::where('ciudad_id', 21)
                ->where('nombre', $aux[0]->nombre)
                ->where('imagen', $aux[0]->imagen)
                ->get(); 

            $nuevoObj=\App\Subcategoria::create([
                'nombre' => $subcategorias[$i]->nombre,
                'ingles' => $subcategorias[$i]->ingles,
                'imagen' => $subcategorias[$i]->imagen,
                'estado' => $subcategorias[$i]->estado,
                'categoria_id' => $aux2[0]->id,
                'ciudad_id' => 21,
            ]);    

        }

        return response()->json(['subcategorias'=>$subcategorias], 200); 
    }

}
