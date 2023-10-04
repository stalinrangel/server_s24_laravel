<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*$producto = \App\Producto::
        with(['ciudades' => function ($query) use ($ciudad_id)  {
            $query->where('ciudad_id', $ciudad_id);
        }])->find($producto_id);*/
        $zona_id=$request->input('zona_id');
        $zona_id=(int)$zona_id;
        
        if (!$request->input('zona_id')){
        //if (true){
           $productos = \App\Producto::with(['establecimiento.usuario.repartidor'=> function ($query) {
                $query->select('id', 'estado', 'activo','ocupado','usuario_id')
                ->with('registro');
            }])
            ->with('subcategoria.categoria')
            ->where('estado','ON')
            ->get();

            $aux=$productos;
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


            $productos=$aux;
        }

        
        if ( $request->input('zona_id')!= null && $request->input('zona_id')!='' && $request->input('zona_id')!=0) {
            
            if ($zona_id==1000) {
               return response()->json(['productos'=>[]], 200); 
            }
            if ($zona_id!=1000) {
                
               $productos = \App\Producto::with(['establecimiento.usuario.repartidor'=> function ($query) {
                $query->select('id', 'estado', 'activo','ocupado','usuario_id')
                
                    ->with(['registro'=> function ($query) {
                    $query->select('id', 'tipo', 'ruc','email','logo','direccion','direccion_exacta','idoneidad','anos_experiencia','urgencias','factura','foto','usuario_id');
                    }]);
                }])
                ->with('establecimiento.usuario.repartidor.registro')
               ->with('subcategoria.categoria')
               ->where('estado','ON')
               //->where('zona_id',$zona_id)
               ->whereHas('zonas2', function ($query) use ($zona_id) {
                    $query->where('zonas_productos.zonas_id', $zona_id);
                })
               ->get(); 
               //return $productos;
            }

            $aux=$productos;
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


            $productos=$aux;
           
       }

       return response()->json(['productos'=>$productos], 200); 
    
        //cargar todas los productos
        /*$productos = \App\Producto::with('establecimiento.usuario.repartidor.registro')->with('subcategoria.categoria')->where('estado','ON')->get();*/

         $aux = [];

            for ($i=0; $i < count($productos) ; $i++) { 
                //if ($productos[$i]->estado == 'ON') {
                  //  if ($productos[$i]->establecimiento->estado == 'ON') {
                    //    if ($productos[$i]->establecimiento->usuario->repartidor->estado == 'ON' && $productos[$i]->establecimiento->usuario->repartidor->activo == 1) {
                            array_push($aux, $productos[$i]);
                      //  }
                    //}
               // }
            }
         $productos=$aux;
         for ($i=0; $i < count($productos) ; $i++) { 
                $calificaciones = \App\Calificacion::where('producto_id',$productos[$i]->id)->with('usuario')->with('producto')->get();
           //     $servicios = \App\Producto::where('establecimiento_id',$productos[$i]->establecimiento->id)->with('subcategoria')->get();
                //return $calificaciones;
                $promedio=0;
                if (count($calificaciones)>0) {
                    $promedio=0;
                    for ($j=0; $j < count($calificaciones); $j++) { 
                        $promedio+=$calificaciones[$j]->puntaje;
                    }
                    $productos[$i]->promedio_calificacion=round($promedio/count($calificaciones), 0, PHP_ROUND_HALF_UP); 
                   // $productos[$i]->calificaciones=$calificaciones;
             //       $productos[$i]->servicios=$servicios;
                }else{
                   $promedio=0;  
                   $productos[$i]->promedio_calificacion=$promedio; 
                 //  $productos[$i]->calificaciones=$calificaciones;
               //    $productos[$i]->servicios=$servicios;
                }
                $img = \App\Calificacion::select('imagen')->where('producto_id',$productos[$i]->id)->whereNotNull('imagen')->get();
                $productos[$i]->fotos2=$img;
            }
        $aux=$productos;
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


            $productos=$aux;
        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 
    }

    public function index2()
    {
        //cargar todas los productos
        $productos = \App\Producto::select('nombre', 'descripcion','imagen')->with('establecimiento.usuario.repartidor')->get();

        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
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
        if ( !$request->input('nombre') ||
            !$request->input('estado') ||
            !$request->input('subcategoria_id') ||
            !$request->input('establecimiento_id'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Faltan datos necesarios para el proceso de alta.'],422);
        } 

        // Comprobamos si la subcategoria que nos están pasando existe o no.
        $subcategoria = \App\Subcategoria::find($request->input('subcategoria_id'));

        if(count($subcategoria)==0){
            return response()->json(['error'=>'No existe la subcategoría con id '.$request->input('subcategoria_id')], 404);          
        } 

        // Comprobamos si el establecimiento que nos están pasando existe o no.
        $establecimiento = \App\Establecimiento::find($request->input('establecimiento_id'));

        if(count($establecimiento)==0){
            return response()->json(['error'=>'No existe el establecimiento con id '.$request->input('establecimiento_id')], 404);          
        }


        //Comprobamos que no exista un producto con las mismas caracteristicas asociado al establecimiento
        $aux = \App\Producto::where('nombre', $request->input('nombre'))
                ->where('establecimiento_id', $request->input('establecimiento_id'))->get();
        if(count($aux)!=0){
           // Devolvemos un código 409 Conflict. 
           // return response()->json(['error'=>'Ya existe un producto con ese nombre asociado al establecimiento.'], 409);
        }

        //Generar código alatorio
        $salt = 'abcdefghijklmnopqrstuvwxyz1234567890';

        $true = true;
        while ($true) {
            $rand = '';
            $i = 0;
            $length = 10;

            while ($i < $length) {
                //Loop hasta que el string aleatorio contenga la longitud ingresada.
                $num = rand() % strlen($salt);
                $tmp = substr($salt, $num, 1);
                $rand = $rand . $tmp;
                $i++;
            }

            $codigo = $rand;

            $auxProd = \App\Producto::where('codigo', $codigo)->get();
            if(count($auxProd)==0){
               $true = false; //romper el bucle
            }
        }

        if ($request->input('zona_id')==null||$request->input('zona_id')=='') {
            $zona_id=1;
        }else{
             $zona_id=$request->input('zona_id');
        }

        if ($request->input('zona_id')) {
            //Verificar que todas las zonas existen
            $zonas = json_decode($request->input('zona_id'));
            for ($i=0; $i < count($zonas) ; $i++) { 
                $aux2 = \App\Zonas::find($zonas[$i]->id);
                if(count($aux2) == 0){
                   // Devolvemos un código 409 Conflict. 
                    //return response()->json(['error'=>'No existe la zona con id '.$zonas[$i]->id], 409);
                }   
            } 
        }

        if($nuevoProducto=\App\Producto::create([
            'nombre' => $request->input('nombre'),
            'estado' => $request->input('estado'),
            'imagen' => $request->input('imagen'),
            'precio' => $request->input('precio'),
            'fotos' => $request->input('fotos'),
            'descripcion' => $request->input('descripcion'),
            'subcategoria_id' => $request->input('subcategoria_id'),
            'establecimiento_id' => $request->input('establecimiento_id'),
            'zona_id' => $zona_id,
            'codigo' => $codigo,
            //'imagen' => $imagen
            'idoneidad' => $request->input('idoneidad'),
            'anos_experiencia' => $request->input('anos_experiencia'),
        ])){

            if ($request->input('zona_id')) {
                //Crear las relaciones en la tabla pivote
                for ($i=0; $i < count($zonas) ; $i++) { 

                    $nuevoProducto->zonas2()->attach($zonas[$i]->id);
                       
                }
            }

            
            $establecimient = \App\Establecimiento::with('usuario')->find($request->input('establecimiento_id'));

            $Notificacion = new \App\Notificacion;
            $Notificacion->mensaje= 'Se ha creado un servicio'.$request->input('nombre');
            $Notificacion->id_operacion=$nuevoProducto->id;
            $Notificacion->usuario_id=$establecimient->usuario_id;
            $Notificacion->accion=9;
            
            try {
                $Notificacion->save();
            } catch (Exception $e) {
                //return response()->json(['error'=>$e], 500);
            }
            $zn = \App\Zonas::where('id',$request->input('zona_id'))->with('ciudad')->first();

            $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $zn->ciudad->id)
                   ->first();

            if ($admin) {
                // Orden del reemplazo
                //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                $order   = array("\r\n", "\n", "\r", " ", "&");
                $replace = array('%20', '%20', '%20', '%20', '%26');

                //Tratar los espacios de la fecha
                $fecha = str_replace($order, $replace, $nuevoProducto->created_at);

                $obj = array('created_at'=>$fecha);
                $obj = json_encode($obj);

                //Tratar los espacios del nombre
                $nombre = str_replace($order, $replace, $request->input('nombre'));

                $this->enviarNotificacion($admin->token_notificacion, 'Se%20ha%20creado%20un%20servicio%20'.$nombre, 0, 6, $obj);    
            }

            

           return response()->json(['message'=>'Producto creado con éxito.',
             'producto'=>$nuevoProducto], 200);

        }else{
            return response()->json(['error'=>'Error al crear el producto.'], 500);
        }
    }

    public function enviarNotificacionProveedor($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        //return "https://service24.app/apii/public/oproveedor.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj;

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

        return $response;
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

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //cargar un producto
        $productos = \App\Producto::with('establecimiento.usuario.repartidor.registro')->where('id',$id)->first();
        $establecimineto=  \App\Establecimiento::where('id',$productos->establecimiento->id)->first();
        $productos->establecimineto=$establecimineto;
        //return count($producto);
        $calificaciones = \App\Calificacion::where('producto_id',$productos->id)/*->where('usuario_id', '!=' ,$establecimineto->usuario_id)*/->with('usuario')->with('producto')->get();
        $servicios = \App\Producto::where('establecimiento_id',$productos->establecimiento->id)->with('subcategoria')->get();
                //return $calificaciones;
        $promedio=0;
        if (count($calificaciones)>0) {
            $promedio=0;
            for ($j=0; $j < count($calificaciones); $j++) { 
                $promedio+=$calificaciones[$j]->puntaje;
            }
            $productos->promedio_calificacion=round($promedio/count($calificaciones), 0, PHP_ROUND_HALF_UP); 
            $productos->calificaciones=$calificaciones;
            $productos->servicios=$servicios;
        }else{
            $productos->promedio_calificacion=0; 
            $productos->calificaciones=$calificaciones;
            $productos->servicios=$servicios;
        }

        $img = \App\Calificacion::select('imagen')->where('producto_id',$productos->id)->whereNotNull('imagen')->get();
        $productos->fotos2=$img;
        
        if(count($productos)==0){
            return response()->json(['error'=>'No existe el producto con id '.$id], 404);          
        }else{
            return response()->json(['producto'=>$productos], 200);
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
        // Comprobamos si el producto que nos están pasando existe o no.
        $producto=\App\Producto::find($id);

        if (count($producto)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el producto con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $precio=$request->input('precio');
        $fotos=$request->input('fotos');
        $descripcion=$request->input('descripcion');
        $subcategoria_id=$request->input('subcategoria_id');
        $estado=$request->input('estado');
        $imagen=$request->input('imagen');
        $anos_experiencia=$request->input('anos_experiencia');
        $zonas=json_decode($request->input('zona_id'));

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($nombre != null && $nombre!='')
        {
            $aux = \App\Producto::where('nombre', $request->input('nombre'))
            ->where('subcategoria_id', $producto->subcategoria_id)
            ->where('establecimiento_id', $producto->establecimiento_id)
            ->where('id', '<>', $producto->id)->get();

            if(count($aux)!=0){
               // Devolvemos un código 409 Conflict. 
                //return response()->json(['error'=>'Ya existe otro producto con el nombre '.$nombre.' asociado al establecimiento.'], 409);
            }

            $producto->nombre = $nombre;
            $bandera=true;
        }

        if ($precio != null && $precio!='')
        {
            $producto->precio = $precio;
            $bandera=true;
        }
        if ($zonas != null && $zonas!='')
        {
            $aux = \App\Zonas_productos::where('producto_id',$id)->get();
                for ($j=0; $j < count($aux); $j++) { 
                    $aux[$j]->delete();
                }
            //return 1;
            for ($i=0; $i < count($zonas) ; $i++) { 
                
                $producto->zonas2()->attach($zonas[$i]->id);
                
            }
        }
        if ($anos_experiencia != null && $anos_experiencia!='')
        {
            $producto->anos_experiencia = $anos_experiencia;
            $bandera=true;
        }

        if ($fotos != null && $fotos!='')
        {
            $producto->fotos = $fotos;
            $bandera=true;
        }

        /*if ($imagen != null && $imagen!='')
        {
            $producto->imagen = $imagen;
            $bandera=true;
        }*/

        if ($descripcion != null && $descripcion!='')
        {
            $producto->descripcion = $descripcion;
            $bandera=true;
        }

        if ($subcategoria_id != null && $subcategoria_id!='')
        {
            // Comprobamos si la subcategoria que nos están pasando existe o no.
            $subcategoria = \App\Subcategoria::find($subcategoria_id);

            if(count($subcategoria)==0){
                return response()->json(['error'=>'No existe la subcategoría con id '.$subcategoria_id], 404);          
            } 

            if ($producto->subcategoria_id != $subcategoria_id) {
                //Comprobar que no exista un producto con el mismo nombre en la nueva subcategoria
                $aux2 = \App\Producto::where('nombre', $producto->nombre)
                ->where('establecimiento_id', $producto->establecimiento_id)
                ->where('subcategoria_id', $subcategoria_id)->get();

                if(count($aux2)!=0){
                   // Devolvemos un código 409 Conflict. 
                   // return response()->json(['error'=>'Ya existe un producto con el nombre '.$producto->nombre.' asociado al establecimiento y a la subcategoría '.$subcategoria->nombre.'.'], 409);
                }
            }

            $producto->subcategoria_id = $subcategoria_id;
            $bandera=true;
        }

        if ($estado != null && $estado!='')
        {
            $producto->estado = $estado;
            $bandera=true;
            $establecimiento=\App\Establecimiento::find($producto->establecimiento_id);
            $usuario = \App\User::find($establecimiento->usuario_id);
            //return $usuario->token_notificacion;
            if ($estado=="ON") {
                $msj="Su servicio ".$producto->nombre." se ha activado y esta listo para obtener pedidos!";
                $msj2="Su%servicio%".$producto->nombre."%se%ha%activado%y%esta%listo%para%obtener%pedidos!";
                
            }else if ($estado=="OFF") {
                $msj="Su servicio ".$producto->nombre." se ha desactivado. Contacta con soporte para mas información.";
                $msj2="Su%servicio%".$producto->nombre."%se%ha%desactivado.%Contacta%con%soporte%para%mas%información.";
            }
            $order   = array("\r\n", "\n", "\r", " ", "&");
            $replace = array('%20', '%20', '%20', '%20', '%26');
            $newstr = str_replace($order, $replace, $msj);
            $re=$this->enviarNotificacionProveedor($usuario->token_notificacion, $newstr, 0, 0, 0); 
            //return response()->json([
              //      're'=>$re], 200);
            /*$establecimiento = \App\Establecimiento::where('id',$producto->establecimiento_id)->with('usuario')->first();
            $Notificacion= new \App\Notificaciones_generales;
            $Notificacion->mensaje= $msj;
            $Notificacion->tipo_usuario=3;
            $Notificacion->ciudad_id=$establecimiento->usuario->ciudad;
            $Notificacion->usuario_id=$establecimiento->usuario->id;
            $Notificacion->save();*/
        }else{
            $producto->estado = 'ED';
        }
        
        if ($imagen != null && $imagen!='')
        {
            $producto->imagen = $imagen;
            $bandera=true;
        }

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($producto->save()) {

                $admin = \App\User::select('token_notificacion')
                   ->where('tipo_usuario', 1)
                   ->where('ciudad', $request->input('ciudad_id'))
                   ->first();

                $establecimient = \App\Establecimiento::with('usuario')->find($producto->establecimiento_id);

                if ($admin) {

                    // Orden del reemplazo
                    //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    //Tratar los espacios del nombre
                    $nombre = str_replace($order, $replace, $request->input('nombre'));

                    $obj = null;

                    $this->enviarNotificacion($admin->token_notificacion, 'Se%20ha%20editado%20un%20servicio'.$nombre, 0, 6, $obj);
                }
                

                $Notificacion= new \App\Notificacion;
                $Notificacion->mensaje= 'Se ha editado un servicio '.$producto->nombre;
                $Notificacion->id_operacion=$producto->id;
                $Notificacion->usuario_id=$establecimient->usuario_id;
                $Notificacion->accion=10;
                
                try {
                    $Notificacion->save();
                } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
                }

                return response()->json(['message'=>'Producto editado con éxito.',
                    'producto'=>$producto], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar el producto.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato al producto.'],409);
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
        // Comprobamos si el producto que nos están pasando existe o no.
        $producto = \App\Producto::find($id);

        if(count($producto)==0){
            return response()->json(['error'=>'No existe el producto con id '.$id], 404);          
        } 

        $pedidos = $producto->pedidos;

        if (sizeof($pedidos) > 0)
        {
            // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'Este producto no puede ser eliminado porque posee pedidos asociados.'], 409);
        }

        // Eliminamos el producto si no tiene relaciones.
        $producto->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el producto.'], 200);
    }

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
    public function productosSubcatEst(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los productos con su subcategoria y establecimineto
        $productos = \App\Producto::with('subcategoria.categoria.catprincipales')
            ->with('establecimiento')
            //->with('zonas')
            //->with('zonas2', function ($query) use ($zonas) {
            //    $query->whereIn('zona_id',$zonas);
            //})
            ->orderBy('updated_at', 'desc')
            ->get();

        /*for ($i=0; $i < count($productos); $i++) { 
            $productos[$i]->zonas=$productos[$i]->zonas2;
        }

        $msgs2=[];
        for ($i=0; $i < count($productos); $i++) {
            for ($k=0; $k < count($productos[$i]->zonas); $k++) { 
                for ($j=0; $j < count($zonas); $j++) { 
                    if ($productos[$i]->zonas[$k]->id==$zonas[$j]) {
                        array_push($msgs2, $productos[$i]);
                    }
                }
            }
        }
        for ($i=0; $i < count($msgs2); $i++) { 
            for ($j=$i+1; $j < count($msgs2)-$i; $j++) { 
                if ($msgs2[$i]->id==$msgs2[$j]->id) {
                    array_splice($msgs2, $j,1);
                }
            }
        }
        $a=[];
        array_push($a, $msgs2[0]);
        for ($i=0; $i < count($msgs2); $i++) { 
            $band=0;
            for ($j=0; $j < count($a); $j++) { 
                if ($a[$j]->id==$msgs2[$i]->id) {
                    $band=1;
                }
            }
            if ($band==0) {
                array_push($a, $msgs2[$i]);
            }
        }
        */
        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 
    }
    public function productosEditados(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los productos con su subcategoria y establecimineto
        $productos = \App\Producto::where('estado','ED')->with('subcategoria.categoria.catprincipales')
            ->with('establecimiento')
            //->with('zonas')
            //->with('zonas2', function ($query) use ($zonas) {
              //  $query->whereIn('zona_id',$zonas);
            //})
            ->orderBy('updated_at', 'desc')
            ->get();
        
        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        }

        for ($i=0; $i < count($productos); $i++) { 
            $productos[$i]->zonas=$productos[$i]->zonas2;
        }

        $msgs2=[];
        for ($i=0; $i < count($productos); $i++) {
            for ($k=0; $k < count($productos[$i]->zonas); $k++) { 
                for ($j=0; $j < count($zonas); $j++) { 
                    if ($productos[$i]->zonas[$k]->id==$zonas[$j]) {
                        array_push($msgs2, $productos[$i]);
                    }
                }
            }
        }
        for ($i=0; $i < count($msgs2); $i++) { 
            for ($j=$i+1; $j < count($msgs2)-$i; $j++) { 
                if ($msgs2[$i]->id==$msgs2[$j]->id) {
                    array_splice($msgs2, $j,1);
                }
            }
        }
        
        if(count($msgs2) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$msgs2], 200);
        } 
    }
    public function on(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los productos con su subcategoria y establecimineto
        $productos = \App\Producto::where('estado','ON')->with('subcategoria.categoria.catprincipales')
            ->with('establecimiento')
            //->with('zonas')
            //->with('zonas2', function ($query) use ($zonas) {
              //  $query->whereIn('zona_id',$zonas);
            //})
            ->orderBy('updated_at', 'desc')
            ->get();
        
        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 

        for ($i=0; $i < count($productos); $i++) { 
            $productos[$i]->zonas=$productos[$i]->zonas2;
        }

        $msgs2=[];
        for ($i=0; $i < count($productos); $i++) {
            for ($k=0; $k < count($productos[$i]->zonas); $k++) { 
                for ($j=0; $j < count($zonas); $j++) { 
                    if ($productos[$i]->zonas[$k]->id==$zonas[$j]) {
                        array_push($msgs2, $productos[$i]);
                    }
                }
            }
        }
        for ($i=0; $i < count($msgs2); $i++) { 
            for ($j=$i+1; $j < count($msgs2)-$i; $j++) { 
                if ($msgs2[$i]->id==$msgs2[$j]->id) {
                    array_splice($msgs2, $j,1);
                }
            }
        }
        
        if(count($msgs2) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$msgs2], 200);
        } 
    }

    public function off(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todos los productos con su subcategoria y establecimineto
        $productos = \App\Producto::where('estado','OFF')->with('subcategoria.categoria.catprincipales')
            ->with('establecimiento')
            //->with('zonas')
            //->with('zonas2', function ($query) use ($zonas) {
              //  $query->whereIn('zona_id',$zonas);
            //})
            ->orderBy('updated_at', 'desc')
            ->get();
        
        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 

        for ($i=0; $i < count($productos); $i++) { 
            $productos[$i]->zonas=$productos[$i]->zonas2;
        }

        $msgs2=[];
        for ($i=0; $i < count($productos); $i++) {
            for ($k=0; $k < count($productos[$i]->zonas); $k++) { 
                for ($j=0; $j < count($zonas); $j++) { 
                    if ($productos[$i]->zonas[$k]->id==$zonas[$j]) {
                        array_push($msgs2, $productos[$i]);
                    }
                }
            }
        }
        for ($i=0; $i < count($msgs2); $i++) { 
            for ($j=$i+1; $j < count($msgs2)-$i; $j++) { 
                if ($msgs2[$i]->id==$msgs2[$j]->id) {
                    array_splice($msgs2, $j,1);
                }
            }
        }
        
        if(count($msgs2) == 0){
            return response()->json(['error'=>'No existen productos.'], 404);          
        }else{
            return response()->json(['productos'=>$msgs2], 200);
        } 
    }

    public function buscarCodigos(Request $request)
    {
        // Primero comprobaremos si estamos recibiendo todos los campos obligatorios.
        if (!$request->input('codigos'))
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Falta el parametro codigos.'],422);
        }

        $codigos = json_decode($request->input('codigos'));

        $productos = [];

        for ($i=0; $i < count($codigos) ; $i++) { 
            $prod = \App\Producto::where('codigo', $codigos[$i]->codigo)
                ->where('estado', 'ON')->with('subcategoria')->get();
            if(count($prod) != 0){
               array_push($productos, $prod[0]);
            }   
        }    

        
        return response()->json(['productos'=>$productos], 200);
        
    }

    /*Cargar los productos asociados a un establecimiento*/
    public function productosEst($establecimiento_id)
    {
        //cargar todos los productos con su subcategoria y establecimineto
        $productos = \App\Producto::with('subcategoria')
            ->with('establecimiento')
            ->where('establecimiento_id', $establecimiento_id)
            ->get();

        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos asociados a este establecimiento.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 
    }

    /*Usada en el panel*/
    public function prodHabSubcatEst($subcategoria_id, $establecimiento_id)
    {
        //cargar todos los productos 
        $productos = \App\Producto::where('estado', 'ON')
            ->where('subcategoria_id', $subcategoria_id)
            ->where('establecimiento_id', $establecimiento_id)
            ->get();

        if(count($productos) == 0){
            return response()->json(['error'=>'No existen productos habilitados para esta subcategoría.'], 404);          
        }else{
            return response()->json(['productos'=>$productos], 200);
        } 
    }

    /*Usada para llenar la tabla zona_productos*/
    public function setTablaZonaProductos()
    {
        //cargar todos los productos 
        $productos = \App\Producto::all();

        for ($i=0; $i < count($productos); $i++) { 

            $productos[$i]->zonas2()->attach($productos[$i]->zona_id);

        }

        return response()->json(['productos'=>$productos], 200); 
    }

    /*Aumenta el contador de vistas*/
    public function countVistas($id)
    {
        //cargar un producto
        $producto = \App\Producto::find($id);

        if(count($producto)==0){

            return response()->json(['error'=>'No existe el producto con id '.$id], 404);

        }else{
            $producto->count_vistas = $producto->count_vistas + 1;  
            $producto->save();                  

            return response()->json(['producto'=>$producto], 200);
        } 
    }
}
