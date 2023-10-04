<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Mail;

class RegistroController extends Controller
{
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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todas las calificaciones
        $repartidores = \App\Repartidor::whereIn('zona_id',$zonas)->select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')->where('activo',4)->
            with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion','zona_id')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato');
                }])->with('establecimiento.productos.subcategoria.categoria.catprincipales')
                   ->with('establecimiento.productos.zonas')->orderBy('id', 'desc')
            ->get();

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$repartidores], 200);
        } 
    }

    public function activos(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todas las calificaciones
        $repartidores = \App\Repartidor::whereIn('zona_id',$zonas)->select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')->where('activo',1)->
            with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion','zona_id')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato');
                }])->with('establecimiento.productos.subcategoria.categoria.catprincipales')
                   ->with('establecimiento.productos.zonas')->orderBy('id', 'desc')
            ->get();

        $rep=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            if ($repartidores[$i]->usuario->registro!=null) {
                array_push($rep, $repartidores[$i]);
            }
        }

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$rep], 200);
        } 
    }

    public function inactivos(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar todas las calificaciones
        $repartidores = \App\Repartidor::whereIn('zona_id',$zonas)->select('id', 'estado', 'activo','ocupado','usuario_id','zona_id')->where('activo',2)->
            with(['usuario' => function ($query){
                    $query->select('id', 'email', 'nombre', 'ciudad', 'estado', 'telefono', 'imagen', 'tipo_usuario', 'token_notificacion','zona_id')
                    ->where(function ($query) {
                        $query
                            ->where('tipo_usuario',3)
                            ->orWhere('tipo_usuario',4);
                    })
                    ->with(['chat_repartidor' => function ($query) {
                        $query->select('id', 'admin_id', 'usuario_id');
                    }])
                    ->with('zonas.ciudad')
                    ->with('registro')
                    ->with('contrato');
                }])->with('establecimiento.productos.subcategoria.categoria.catprincipales')
                   ->with('establecimiento.productos.zonas')->orderBy('id', 'desc')
            ->get();

        $rep=[];
        for ($i=0; $i < count($repartidores); $i++) { 
            if ($repartidores[$i]->usuario->registro!=null) {
                array_push($rep, $repartidores[$i]);
            }
        }

        if(count($repartidores) == 0){
            return response()->json(['error'=>'No existen repartidores.'], 404);          
        }else{
            return response()->json(['repartidores'=>$rep], 200);
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
        
        
        $pedido = \App\User::find($request->input('usuario_id'));
        if(count($pedido) == 0){
           // Devolvemos un código 409 Conflict. 
            return response()->json(['error'=>'este usuario ya tiene un registro.'], 409);
        }

        //Calificar el pedido
        if($Registro=\App\Registro::create($request->all())){

           $Repartidor= \App\Repartidor::where('usuario_id',$Registro->usuario_id)->first();
           $Repartidor->activo=4;
           $Repartidor->save();

           $usuario = \App\User::find($Registro->usuario_id);

           $usuario->imagen=$Registro->foto;

           $usuario->save();

           $establecimiento= \App\Establecimiento::where('usuario_id',$Registro->usuario_id)->first();
           return response()->json(['message'=>'Registro éxito.',
             'Registro'=>$Registro,
             'Repartidor'=>$Repartidor,
             'establecimiento'=>$establecimiento,
              ], 200);
        }else{
            return response()->json(['error'=>'Error al crear Registro.'], 500);
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
        //cargar una calificacion
        $Registro = \App\Registro::find($id);

        if(count($Registro)==0){
            return response()->json(['error'=>'No existe Registro con id '.$id], 404);          
        }else{
            return response()->json(['Registro'=>$Registro], 200);
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
        $Registro = \App\Registro::where('id',$id)->first();

        
        //$Registro->fill($request->all());
        
        //$Registro=\App\Registro::create($request->all())
        //return response()->json(['Registro'=>$Registro], 200);
        if(count($Registro)==0){
            return response()->json(['error'=>'No existe registro con id '.$id], 404);          
        }

        $usuario=\App\User::find($Registro->usuario_id);

        if(1)
        {
            $Registro->contrato=$request->input('contrato');
            // Almacenamos en la base de datos el registro.
            if ($Registro->save()) {

                $this->contrato($usuario->email,$Registro->contrato);

                return response()->json(['message'=>'registro editada con éxito.',
                    'Registro'=>$Registro], 200);
            }else{
                return response()->json(['error'=>'Error al actualizar registro.'], 500);
            }
            
        }
        else
        {
            // Se devuelve un array errors con los errores encontrados y cabecera HTTP 304 Not Modified – [No Modificada] Usado cuando el cacheo de encabezados HTTP está activo
            // Este código 304 no devuelve ningún body, así que si quisiéramos que se mostrara el mensaje usaríamos un código 200 en su lugar.
            return response()->json(['error'=>'No se ha modificado ningún dato a la la calificación.'],409);
        }            
        
    }

    public function contrato($email,$contrato)
    {
        $enlace = $contrato;

        $data = array( 'enlace' => $enlace);

        //Enviamos el correo con el enlace para validar
        Mail::send('contrato.contrato', $data, function($msj) use ($email){
            $msj->subject('Contrato Service24');
            $msj->from('service24@gmail.com', 'Service24');
            $msj->to($email);
        });
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
        $Registro=\App\Registro::find($id);

        if(count($Registro)==0){
            return response()->json(['error'=>'No existe la Registro con id '.$id], 404);          
        }
        
        // Eliminamos la Registro del pedido.
        $Registro->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente Registro.'], 200);
    }
}
