<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class ZonasController extends Controller
{


    public function usuario_zona($id)
    {

        //cargar todas las coordenadas
        $usuario = \App\User::with('zonas.ciudad')->find($id);

        if(count($usuario) == 0){
            return response()->json(['error'=>'No existen zona ni ciudad.'], 404);          
        }else{
            return response()->json(['zona_id'=>$usuario->zonas->id,'ciudad_id'=>$usuario->zonas->ciudad->id], 200);
        }
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->input('ciudad_id')) {
            //cargar todas las coordenadas
            $coordenadas = \App\Zonas::/*where('ciudad_id',$request->input('ciudad_id'))->with('pais')->with('ciudad')->*/get();

            if(count($coordenadas) == 0){
                return response()->json(['error'=>'No existen zonas.'], 404);          
            }else{
                return response()->json(['coordenadas'=>$coordenadas], 200);
            }
        }else{
            //cargar todas las coordenadas
            $coordenadas = \App\Zonas::with('pais')->with('ciudad')->get();

            if(count($coordenadas) == 0){
                return response()->json(['error'=>'No existen zonas.'], 404);          
            }else{
                return response()->json(['coordenadas'=>$coordenadas], 200);
            }
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
        if ( !$request->input('nombre') )
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Falta el parámetro nombre.'],422);
        } 

        if ( !$request->input('coordenadas') )
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
            return response()->json(['error'=>'Falta el parámetro coordenadas.'],422);
        } 

        if ( !$request->input('costo') )
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para errores de validación.
           // return response()->json(['error'=>'Falta el parámetro costo.'],422);
        } 
        

        if($nuevaCiudad=\App\Zonas::create($request->all())){

           return response()->json(['message'=>'Ciudad creada con éxito.',
             'ciudad'=>$nuevaCiudad], 200);
        }else{
            return response()->json(['error'=>'Error al crear la ciudad.'], 500);
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
        // Comprobamos si la ciudad que nos están pasando existe o no.
        $zonas=\App\Zonas::find($id);

        if (count($zonas)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe la ciudad con id '.$id], 404);
        }      

        // Listado de campos recibidos teóricamente.
        $nombre=$request->input('nombre');
        $coordenadas=$request->input('coordenada');
        $costo=$request->input('costo');
        $ciudad_id=$request->input('ciudad_id');

        // Creamos una bandera para controlar si se ha modificado algún dato.
        $bandera = false;

        // Actualización parcial de campos.
        if ($nombre != null && $nombre!='')
        {
            $zonas->nombre = $nombre;
            $bandera=true;
        }

        if ($coordenadas != null && $coordenadas!='')
        {
            $zonas->coordenadas = $coordenadas;
            $bandera=true;
        }

        if ($costo != null && $costo!='')
        {
            $zonas->costo = $costo;
            $bandera=true;
        } 

        if ($ciudad_id != null && $ciudad_id!='')
        {
            $zonas->ciudad_id = $ciudad_id;
            $bandera=true;
        } 

        if ($bandera)
        {
            // Almacenamos en la base de datos el registro.
            if ($zonas->save()) {

                return response()->json(['message'=>'Zona editada con éxito.',
                    'zona'=>$zona], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar la ciudad.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la ciudad.'],409);
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
        $zona=\App\Zonas::find($id);

        if ($zonas->delete()) {
            return response()->json(['message'=>'Se ha eliminado correctamente el blog.'], 200);
        }else{
            return response()->json(['message'=>'Se no se pudo eliminar la zona.'], 409);
        }

        
    }

    public function zonaProductos($id)
    {
        //cargar una zona
        $zona = \App\Zonas::with('productos')->find($id);

        if(count($zona)==0){
            return response()->json(['error'=>'No existe la zona con id '.$id], 404);          
        }else{

            return response()->json(['zona'=>$zona], 200);
        }
    }

    public function detectar(Request $request){
        //$coordenadas = \App\Zonas::get();
        //$inputData = json_encode($coordenadas);
        //return $inputData;
        //$inputData = '{"coordenadas":[{"id":67,"nombre":"Ciudad de la Costa","coordenadas":"[{\"lat\":-34.87400936218177,\"lng\":-56.017423864483824},{\"lat\":-34.865735113863856,\"lng\":-56.060707593366494},{\"lat\":-34.81413254823717,\"lng\":-56.033629163663825},{\"lat\":-34.818944117575796,\"lng\":-56.00900713450461},{\"lat\":-34.83023695913505,\"lng\":-56.009164085641494},{\"lat\":-34.81030774497463,\"lng\":-55.965728288080534},{\"lat\":-34.80481175369268,\"lng\":-55.96608527333661},{\"lat\":-34.79582060621335,\"lng\":-55.95437005491181},{\"lat\":-34.777236007624936,\"lng\":-55.92086809084936},{\"lat\":-34.786014522666385,\"lng\":-55.890973027359685},{\"lat\":-34.79636408011316,\"lng\":-55.86181640953942}]","costo":0,"ciudad_id":3,"pais_id":1,"updated_at":"2023-09-22 17:08:42","created_at":"0000-00-00 00:00:00","pais":{"id":1,"nombre":"Uruguay","updated_at":"2020-04-17 19:25:38","created_at":"-0001-11-30 00:00:00"},"ciudad":{"id":3,"nombre":"Canelones","pais_id":1,"updated_at":"2020-03-27 20:04:37","created_at":"-0001-11-30 00:00:00"}},{"id":68,"nombre":"Costa de Oro","coordenadas":"[{\"lat\":-34.784550859184364,\"lng\":-55.890366596665224},{\"lat\":-34.77406045062654,\"lng\":-55.88883798898383},{\"lat\":-34.75304264492318,\"lng\":-55.85931935676559},{\"lat\":-34.73586242174523,\"lng\":-55.7837069207169},{\"lat\":-34.722987822780155,\"lng\":-55.77837433296441},{\"lat\":-34.73488838081883,\"lng\":-55.75430578182637},{\"lat\":-34.73446397897049,\"lng\":-55.72866805459558},{\"lat\":-34.7228322124505,\"lng\":-55.717195324629536},{\"lat\":-34.72458996350168,\"lng\":-55.699658371537915},{\"lat\":-34.726940330284535,\"lng\":-55.65560401047765},{\"lat\":-34.75758663030165,\"lng\":-55.63249573207646},{\"lat\":-34.775150039699774,\"lng\":-55.633993745811274},{\"lat\":-34.78114201469511,\"lng\":-55.64828549840673},{\"lat\":-34.78345757354418,\"lng\":-55.760286898382},{\"lat\":-34.796724134115955,\"lng\":-55.859448102798304}]","costo":0,"ciudad_id":3,"pais_id":1,"updated_at":"2023-09-22 17:13:28","created_at":"0000-00-00 00:00:00","pais":{"id":1,"nombre":"Uruguay","updated_at":"2020-04-17 19:25:38","created_at":"-0001-11-30 00:00:00"},"ciudad":{"id":3,"nombre":"Canelones","pais_id":1,"updated_at":"2020-03-27 20:04:37","created_at":"-0001-11-30 00:00:00"}}]}';
       
        //$data = json_decode($inputData, true);
        //$areas = $data['coordenadas'];
        $areas = \App\Zonas::get();
        //$lat = -34.87400936218177;
        //$lng = -56.017423864483824;
        $lat = $request->input('lat');
        $lng = $request->input('lng');
        //return $request->input('lng');
        $closestArea = null;
        $closestDistance = INF;
        foreach ($areas as $area) {
            $coordinates = json_decode($area['coordenadas'], true);
            foreach ($coordinates as $coordinate) {
                $distance = $this->haversine($lat, $lng, $coordinate['lat'], $coordinate['lng']);
                if ($distance < $closestDistance) {
                    $closestArea = $area;
                    $closestDistance = $distance;
                }
            }
        }
        if ($closestDistance > 5) {
            return -1;
        } else {
            return $closestArea;
        }
    }

    function getArea($lat, $lng, $areas) {
        $closestArea = null;
        $closestDistance = INF;
        foreach ($areas as $area) {
            $distance = $this->haversine($lat, $lng, $area['lat'], $area['lng']);
            if ($distance < $closestDistance) {
                $closestArea = $area;
                $closestDistance = $distance;
            }
        }
        return $closestArea;
    }

    function haversine($lat1, $lng1, $lat2, $lng2) {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);
        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng/2) * sin($dLng/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;
        return $distance;
    }
}

