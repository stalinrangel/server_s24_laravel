<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class CatprincipalesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $categorias = \App\Catprincipales::/*where('ciudad_id',$request->input('ciudad_id'))->*/with('ciudad.pais')->with('categorias')->get();
        }else{
            $categorias = \App\Catprincipales::with('ciudad.pais')->with('categorias')->get();
        }
        

        if(count($categorias) == 0){
            return response()->json(['error'=>'No existen categorías.'], 404);          
        }else{
            return response()->json(['categorias'=>$categorias], 200);
        } 
    }

    public function replicar_categorias(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $categorias = \App\Catprincipales::/*where('ciudad_id',$request->input('ciudad_id'))->*/with('categorias.subcategorias')->get();
        }
        
        for ($i=0; $i < count($categorias); $i++) {
            $principales=new \App\Catprincipales; 
            $principales->orden=$categorias[$i]->orden;
            $principales->nombre=$categorias[$i]->nombre;
            $principales->ingles=$categorias[$i]->ingles;
            $principales->imagen=$categorias[$i]->imagen;
            $principales->estado=$categorias[$i]->estado;
            $principales->ciudad_id=$request->input('nueva_ciudad_id');

            if ($principales->save()) {
                for ($j=0; $j < count($categorias[$i]->categorias); $j++) { 
                    $categ=new \App\Categoria; 
                    $categ->nombre=$categorias[$i]->categorias[$j]->nombre;
                    $categ->ingles=$categorias[$i]->categorias[$j]->ingles;
                    $categ->imagen=$categorias[$i]->categorias[$j]->imagen;
                    $categ->estado=$categorias[$i]->categorias[$j]->estado;
                    $categ->ciudad_id=$request->input('nueva_ciudad_id');
                    $categ->catprincipales_id=$principales->id;

                    if ($categ->save()) {
                        for ($k=0; $k < count($categorias[$i]->categorias[$j]->subcategorias); $k++) { 
                            $subcateg=new \App\Subcategoria; 
                            $subcateg->nombre=$categorias[$i]->categorias[$j]->subcategorias[$k]->nombre;
                            $subcateg->ingles=$categorias[$i]->categorias[$j]->subcategorias[$k]->ingles;
                            $subcateg->imagen=$categorias[$i]->categorias[$j]->subcategorias[$k]->imagen;
                            $subcateg->estado=$categorias[$i]->categorias[$j]->subcategorias[$k]->estado;
                            $subcateg->ciudad_id=$request->input('nueva_ciudad_id');
                            $subcateg->categoria_id=$categ->id;
                            if ($subcateg->save()) {
                                # code...
                            }else{
                                return response()->json(['error'=>'Error al crear sub'], 404);  
                            }
                        }
                    }else{
                            return response()->json(['error'=>'Error al crear categ'], 404);  
                        }
                }
            }else{
                return response()->json(['error'=>'Error al crear principales'], 404);  
            }
        }


        $categoriasF = \App\Catprincipales::where('ciudad_id',$request->input('nueva_ciudad_id'))->with('categorias.subcategorias')->get();


        if(count($categoriasF) == 0){
            return response()->json(['error'=>'No existen categorías.'], 404);          
        }else{
            return response()->json(['categorias'=>$categoriasF], 200);
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
        if ( !$request->input('nombre'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 
        
        $aux = \App\Catprincipales::where('nombre', $request->input('nombre'))->get();
        if(count($aux)!=0){
           // Devolvemos un código 409 Conflict. 
           // return response()->json(['error'=>'Ya existe una categoría con ese nombre.'], 409);
        } 

        if($nuevaCategoria=\App\Catprincipales::create($request->all())){
           return response()->json(['message'=>'Categoría creada con éxito.',
             'categoria'=>$nuevaCategoria], 200);
        }else{
            return response()->json(['error'=>'Error al crear la categoría.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    /*public function show($id)
    {
        //cargar una cat
        //$categoria = \App\Categoria::with('subcategorias.productos.establecimiento')->find($id);

        $categoria = \App\Categoria::with('subcategorias')->find($id);

        if(count($categoria)==0){
            return response()->json(['error'=>'No existe la categoría con id '.$id], 404);          
        }else{

            $aux = [];

            for ($i=0; $i < count($categoria->subcategorias); $i++) { 

                $subcategoria_id = $categoria->subcategorias[$i]->id;

                $estbl = 
                \App\Establecimiento::with(['productos' => function ($query) use ($subcategoria_id){
                    $query->where('subcategoria_id', $subcategoria_id);
                }])->get();

                

                $categoria->subcategorias[$i]->establecimientos = [];
                for ($j=0; $j < count($estbl) ; $j++) { 
                    if (count($estbl[$j]->productos) > 0) {
                        //array_push($categoria->subcategorias[$i]->establecimientos, $estbl[$j]);
                        array_push($aux, $estbl[$j]);
                    }
                }
                    $categoria->subcategorias[$i]->establecimientos = $aux;
                    $aux = [];
                //return response()->json(['estbl'=>$estbl], 200);

            }

            
            return response()->json(['categoria'=>$categoria], 200);
        } 
    }*/

    public function categorias(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $categorias = \App\Catprincipales::where('estado', 'ON')->/*where('ciudad_id',$request->input('ciudad_id'))->*/with('ciudad.pais')->with(['categorias' => function ($query){
                    $query->where('estado', 'ON')
                        ->with(['subcategorias' => function ($query){
                            $query->where('estado', 'ON');
                        }]);
                    }])->OrderBy('orden')->get();
        }else{
            $categorias = \App\Catprincipales::where('estado', 'ON')->with('ciudad.pais')->with(['categorias' => function ($query){
                    $query->where('estado', 'ON')
                        ->with(['subcategorias' => function ($query){
                            $query->where('estado', 'ON');
                        }]);
                    }])->OrderBy('orden')->get();
        }

        

        if(count($categorias) == 0){
            return response()->json(['error'=>'No existen categorías habilitadas.'], 404);          
        }else{
            return response()->json(['catprincipales'=>$categorias], 200);
        }   
    }

    
    public function show($id)
    {
        //cargar una cat
        $categoria = \App\Catprincipales::with(['subcategorias' => function ($query){
                    $query->where('estado', 'ON');
                }])->find($id);

        if(count($categoria)==0){
            return response()->json(['error'=>'No existe la categoría con id '.$id], 404);          
        }else{
             //return response()->json(['subcategorias'=>$categoria], 200);
            $aux = [];
            $subcatAux = [];

            $nowday = date('N');
            $nowhour = date('H');

            for ($i=0; $i < count($categoria->subcategorias); $i++) { 

                $subcategoria_id = $categoria->subcategorias[$i]->id;
                //whereRaw("created_at >= ? AND created_at <= ?", array($from->format('Y-m-d') ." 00:00:00", $to->format('Y-m-d')." 23:59:59"))
                
                //return response()->json(['now'=>$nowhour], 200); 
                $estbl = 
                \App\Establecimiento::with(['productos' => function ($query) use ($subcategoria_id){
                    $query->where('subcategoria_id', $subcategoria_id)
                        ->where('estado', 'ON');
                }])->where('estado', 'ON')->get();     

                $categoria->subcategorias[$i]->establecimientos = [];
                for ($j=0; $j < count($estbl) ; $j++) { 
                    //return response()->json(['estbl'=>$estbl], 200); 
                    if (count($estbl[$j]->productos) > 0) {
                        //array_push($aux, $estbl[$j]);
                        //return $nowday;
                        //return $nowhour;
                        if ($nowday==1) {
                            if ($nowhour >= $estbl[$j]->lunes_i && $nowhour <= $estbl[$j]->lunes_f) {
                                /*if ($estbl[$j]->nombre=='A QUI ME QUEDO') {
                                    return response()->json(['estbl[$j]->lunes_i'=>$estbl[$j],'nowday'=>$nowday,'nowhour'=>$nowhour], 200);
                                }*/
                                 
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==2) {
                            if ($nowhour >= $estbl[$j]->martes_i && $nowhour <= $estbl[$j]->martes_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==3) {
                            if ($nowhour >= $estbl[$j]->miercoles_i && $nowhour <= $estbl[$j]->miercoles_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==4) {
                            if ($nowhour >= $estbl[$j]->jueves_i && $nowhour <= $estbl[$j]->jueves_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==5) {
                            if ($nowhour >= $estbl[$j]->viernes_i&& $nowhour <= $estbl[$j]->viernes_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==6) {
                            if ($nowhour >= $estbl[$j]->sabado_i && $nowhour <= $estbl[$j]->sabado_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                        else if ($nowday==7) {
                            if ($nowhour >=$estbl[$j]->domingo_i && $nowhour <=$estbl[$j]->domingo_f) {
                                array_push($aux, $estbl[$j]);
                            }
                        }
                    }
                }

                $categoria->subcategorias[$i]->establecimientos = $aux;

                $aux = [];
            }

            for ($i=0; $i < count($categoria->subcategorias); $i++) { 
                if (count($categoria->subcategorias[$i]->establecimientos)>0) {
                    array_push($subcatAux,$categoria->subcategorias[$i]);
                }
            }

            return response()->json(['subcategorias'=>$subcatAux], 200);
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
        // Comprobamos si la categoria que nos están pasando existe o no.
        $categoria=\App\Catprincipales::find($id);

        if (count($categoria)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la categoría con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $ingles=$request->input('ingles');
        $imagen=$request->input('imagen');
        $estado=$request->input('estado');
        $subcategorias=$request->input('subcategorias');
        $ciudad_id=$request->input('ciudad_id');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($nombre != null && $nombre!='')
        {
            $aux = \App\Catprincipales::where('nombre', $request->input('nombre'))
            ->where('id', '<>', $categoria->id)->get();

            if(count($aux)!=0){
               // Devolvemos un código 409 Conflict. 
               // return response()->json(['error'=>'Ya existe otra categoría con ese nombre.'], 409);
            }

            $categoria->nombre = $nombre;
            $bandera=true;
        }

        if ($ingles != null && $ingles!='')
        {
            $categoria->ingles = $ingles;
            $bandera=true;
        }

        if ($ciudad_id != null && $ciudad_id!='')
        {
            $categoria->ciudad_id = $ciudad_id;
            $bandera=true;
        }

        if ($imagen != null && $imagen!='')
        {
            $categoria->imagen = $imagen;
            $bandera=true;
        }

        if ($estado != null && $estado!='')
        {

            if ($estado == 'OFF') {
                $subcategorias = $categoria->subcategorias;

                if (sizeof($subcategorias) > 0)
                {
                    for ($i=0; $i < count($subcategorias) ; $i++) { 
                        $subcategorias[$i]->estado = $estado;
                        $subcategorias[$i]->save();

                        $productos = $subcategorias[$i]->productos;
                        if (sizeof($productos) > 0) {
                            for ($j=0; $j < count($productos); $j++) { 
                                $productos[$j]->estado = $estado;
                                $productos[$j]->save();
                            }
                        }
                    }
                }
            }

            $categoria->estado = $estado;
            $bandera=true;
        }

        if (sizeof($subcategorias) > 0 )
        {
            $bandera=true;

            $subcategorias = json_decode($subcategorias);
            for ($i=0; $i < count($subcategorias) ; $i++) {

                if ($subcategorias[$i]->estado == 'ON') {

                    $subcat = \App\Subcategoria::find($subcategorias[$i]->id);

                    if(count($subcat) == 0){
                       // Devolvemos un código 409 Conflict. 
                        return response()->json(['error'=>'No existe la subcategoria con id '.$subcategorias[$i]->id], 409);
                    }else{
                        $subcat->estado = $subcategorias[$i]->estado;
                        $subcat->save();

                        $productos = $subcat->productos;
                        if (sizeof($productos) > 0) {
                            for ($j=0; $j < count($productos); $j++) { 
                                $productos[$j]->estado = $estado;
                                $productos[$j]->save();
                            }
                        }
                    }
                }  
            }
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($categoria->save()) {
                return response()->json(['message'=>'Categoría editada con éxito.',
                    'categoria'=>$categoria], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la categoría.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la categoría.'],409);
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
        // Comprobamos si la categoria existe o no.
        $categoria=\App\Catprincipales::find($id);

        if (count($categoria)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la categoría con id '.$id], 404);
        }
       
        $subcategorias = $categoria->subcategorias;

        if (sizeof($subcategorias) > 0)
        {
            // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'Esta categoría no puede ser eliminada porque posee subcategorías asociadas.'], 409);
        }

        // Eliminamos la categoria si no tiene relaciones.
        $categoria->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente la categoría.'], 200);
    }

    public function catsSubcatsProdsEst()
    {
        //cargar todas las categorias con sus subcats productos y su establecimiento
        $categorias = \App\Catprincipales::with('subcategorias.productos.establecimiento')->get();

        if(count($categorias) == 0){
            return response()->json(['error'=>'No existen categorías.'], 404);          
        }else{
            return response()->json(['categorias'=>$categorias], 200);
        } 
    }

    public function categoriaSubcategorias($id)
    {
        //cargar una cat con sus subcat
        $categoria = \App\Catprincipales::with('subcategorias')->find($id);

        if(count($categoria)==0){
            return response()->json(['error'=>'No existe la categoría con id '.$id], 404);          
        }else{

            //cargar las subcat de la cat
            //$categoria = $categoria->with('subcategorias')->get();
            //$categoria->productos = $categoria->productos()->get();
            //$categoria = $categoria->subcategorias;

            return response()->json(['categoria'=>$categoria], 200);
        } 
    }

    //Usada en el panel
    public function categoriasHabilitadas(Request $request)
    {
        if ($request->input('ciudad_id')) {
            $categorias = \App\Catprincipales::where('estado', 'ON')->/*where('ciudad_id',$request->input('ciudad_id'))->*/with('ciudad.pais')->with('categorias')->get();
        }else{
            $categorias = \App\Catprincipales::where('estado', 'ON')->with('ciudad.pais')->with('categorias')->get();
        }
        //cargar todas las cat en estado ON
        //$categorias = \App\Catprincipales::where('estado', 'ON')->get();

        if(count($categorias) == 0){
            return response()->json(['error'=>'No existen categorías habilitadas.'], 404);          
        }else{
            return response()->json(['categorias'=>$categorias], 200);
        }   
    }
}
