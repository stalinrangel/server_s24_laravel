<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class DatosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //cargar todas las calificaciones
        $calificaciones = \App\Datos::all();
         $mun2 = \App\Estados::all();
        // return response()->json(['datos'=>$mun2], 200);
        if(count($calificaciones) == 0){
            return response()->json(['error'=>'No existen calificaciones.'], 404);          
        }else{

            /*$munic=$calificaciones;
            $mun=[];
            $mun2=[];
            array_push($mun2,$munic[0]);
            $band=0;
            for($i=0; $i < count($munic); $i++) { 
                for($j=0; $j < count($mun2); $j++) { 
                    if($mun2[$j]->d_estado==$munic[$i]->d_estado){
                        $band=1;
                    }
                }
                if($band==0){
                    array_push($mun2,$munic[$i]);
                    $estado=new \App\Estados;
                    $estado->nombre=$munic[$i]->d_estado;
                    $estado->save();
                }
                $band=0;
            }

            
            return response()->json(['datos'=>$mun2], 200);*/
            for($i=0; $i < count($mun2); $i++) { 
                for($j=0; $j < count($calificaciones); $j++) { 
                    if($mun2[$i]->nombre==$calificaciones[$j]->d_estado){
                        $muncipio=new \App\Municipios;
                        $muncipio->nombre=$calificaciones[$j]->D_mnpio;
                        $muncipio->estado_id=$mun2[$i]->id;
                        $muncipio->save();

                        $localidades=new \App\Localidades;
                        $localidades->nombre=$calificaciones[$j]->d_asenta;
                        $localidades->codigo_postal=$calificaciones[$j]->d_codigo;
                        $localidades->municipio_id=$muncipio->id;
                        $localidades->save();
                    }
                }
            }
            return response()->json(['datos'=>$mun2], 200);
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

}
