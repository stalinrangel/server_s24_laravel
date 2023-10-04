<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use DB;

date_default_timezone_set('America/Panama');

class ChatRepartidorController extends Controller
{
    //Enviar notificacion a un dispositivo repartidor/panel mediante su token_notificacion
    public function enviarNotificacionProveedor($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        

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

        //return $response;
    }
    //Enviar notificacion a un dispositivo repartidor/panel mediante su token_notificacion
    public function enviarNotificacion($token_notificacion, $msg, $pedido_id = 'null', $accion = 0, $obj = 'null')
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/onesignal.php?contenido=".$msg."&token_notificacion=".$token_notificacion."&pedido_id=".$pedido_id."&accion=".$accion."&obj=".$obj);
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
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //cargar todos los chats
        $zonas=$this->ciudad($request->input('ciudad_id'));
        $chats = \App\ChatRepartidor::
            with(['usuario' => function ($query) use ($zonas) {
                $query->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion')
                ->whereIn('zona_id',$zonas);
            }])
            ->where('ciudad_id', $request->input('ciudad_id'))
            ->orderBy('id', 'desc')
            ->get();

        $chataux=[];
        for ($i=0; $i < count($chats); $i++) { 
            try {
                if ($chats[$i]->usuario!=null) {                    
                    array_push($chataux,$chats[$i]);
                    }
            } catch (Exception $e) {
                    //return response()->json(['error'=>$e], 500);
            }
        }
        
        $chats=$chataux;

        if(count($chats)!=0){
            //Cargar el ultimo mensaje
            for ($i=0; $i < count($chats) ; $i++) { 
                $chats[$i]->ultimo_msg = $chats[$i]
                    ->mensajes()
                    ->select('id', 'msg', 'created_at')
                    ->orderBy('id', 'desc')
                    ->take(1)->first(); 
            }          
        }

        return response()->json(['chats'=>$chats], 200);
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
        
    }

    /*crear un mesage asociado a un chat*/
    public function storeMsg(Request $request)
    {
        $oneSignal = 'sinEjecutar';

        // Primero comprobaremos si estamos recibiendo todos los campos.
        if ( !$request->input('emisor_id') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro emisor_id.'],422);
        }
        if ( !$request->input('receptor_id') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro receptor_id.'],422);
        }
        if ( !$request->input('msg') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro msg.'],422);
        }
        if ( !$request->input('emisor') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro emisor.'],422);
        }
        if ( !$request->input('token_notificacion') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            //return response()->json(['error'=>'Falta el parametro token_notificacion.'],422);
        }
        if ( !$request->input('created_at') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro created_at.'],422);
        }
        if ( !$request->input('ciudad_id') )
        {
            // Se devuelve un array error con los errors encontrados y cabecera HTTP 422 Unprocessable Entity – [Entidad improcesable] Utilizada para messagees de validación.
            return response()->json(['error'=>'Falta el parametro ciudad_id.'],422);
        }

        //Verificar si existe un chat entre el admin y el repartidor
        if ($request->input('chat_id') != null && $request->input('chat_id') != '') {
            $chat = \App\ChatRepartidor::find($request->input('chat_id'));
            $bandera = false;
        }
        else{

            if ($request->input('emisor') == 'admin') {

                $chat = \App\ChatRepartidor::
                    where(function ($query) use ($request) {
                        $query->where('usuario_id', $request->input('receptor_id'))
                              ->where('ciudad_id', $request->input('ciudad_id'));
                    })
                    ->get();

            }else if ($request->input('emisor') == 'repartidor') {

                $chat = \App\ChatRepartidor::
                    where(function ($query) use ($request) {
                        $query->where('usuario_id', $request->input('emisor_id'))
                              ->where('ciudad_id', $request->input('ciudad_id'));
                    })
                    ->get();
            }

            /*$chat = \App\ChatRepartidor::
                where(function ($query) use ($request) {
                    $query->where('admin_id', $request->input('emisor_id'))
                          ->where('usuario_id', $request->input('receptor_id'));
                })
                ->orWhere(function ($query) use ($request) {
                    $query->where('admin_id', $request->input('receptor_id'))
                          ->where('usuario_id', $request->input('emisor_id'));
                })
                ->get();*/

                $bandera = true;
        }

        
        if(count($chat)==0){

            //Crear el nuevo chat
            if ($request->input('emisor') == 'admin') {

                $chat=\App\ChatRepartidor::create([
                        'admin_id' => $request->input('emisor_id'),
                        'usuario_id' => $request->input('receptor_id'),
                        'ciudad_id' => $request->input('ciudad_id'),
                    ]);

            }else if ($request->input('emisor') == 'repartidor') {

                $chat=\App\ChatRepartidor::create([
                        'admin_id' => $request->input('receptor_id'),
                        'usuario_id' => $request->input('emisor_id'),
                        'ciudad_id' => $request->input('ciudad_id'),
                    ]);
            }

            //Crear el mensaje asociado al nuevo chat
            if ($request->input('emisor') == 'admin') {

               $msg = $chat->mensajes()->create([
                    'msg' => $request->input('msg'),
                    'emisor_id' => $chat->admin_id,
                    'receptor_id' => $chat->usuario_id,
                    'created_at' => $request->input('created_at'),
                ]);

               if ($request->input('token_notificacion') != '' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != 'null') {

                    /*$explode1 = explode(" ",$request->input('msg'));
                    $auxMsg = null;
                    for ($i=0; $i < count($explode1); $i++) { 
                        $auxMsg = $auxMsg.$explode1[$i].'%20'; 
                    }*/

                    // Orden del reemplazo
                    //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');;

                    // Procesa primero \r\n así no es convertido dos veces.
                    $newstr = str_replace($order, $replace, $request->input('msg'));

                    $emisor = \App\User::select('id', 'nombre', 'tipo_usuario', 'token_notificacion', 'imagen')->find($msg->emisor_id);

                    //Tratar los espacios del nombre
                    $nombre = str_replace($order, $replace, $emisor->nombre);
                    $emisor->nombre = $nombre;

                    //Tratar los & de la imagen
                    $imagen = str_replace('&', '%26', $emisor->imagen);
                    $emisor->imagen = $imagen;

                    //Tratar los espacios de la fecha del mensaje
                    $created_at = str_replace($order, $replace, $msg->created_at);
                    $msgAux = array('id'=>$msg->id, 'estado'=>$msg->estado,
                        'chat_id'=>$msg->chat_id, 'emisor_id'=>$msg->emisor_id,
                        'receptor_id'=>$msg->receptor_id, 'created_at'=>$created_at);

                    $obj = array('chat_id'=>$msg->chat_id, 'emisor'=>$emisor, 'msg'=>$msgAux);
                    $obj = json_encode($obj);

                    $oneSignal = $this->enviarNotificacionProveedor($request->input('token_notificacion'), $newstr, 'null', 2, $obj);

                    //Enviar a los demas admins
                    //Enviar a los demas admins
                    $admins = \App\User::select('token_notificacion')
                       ->where(function ($query) {
                            $query
                                ->where('tipo_usuario', 1)
                                ->orwhere('tipo_usuario', 5)
                                ->orwhere('tipo_usuario', 6);
                        })
                       ->where('ciudad', $request->input('ciudad_id'))
                       ->where('id', '<>', $request->input('emisor_id'))
                       ->get();

                       $this->enviarNotificacion($admins[0]->token_notificacion, $newstr, 'null', 2, $obj);

                }

            }else if ($request->input('emisor') == 'repartidor') {

               $msg = $chat->mensajes()->create([
                    'msg' => $request->input('msg'),
                    'emisor_id' => $chat->usuario_id,
                    'receptor_id' => $chat->admin_id,
                    'created_at' => $request->input('created_at'),

                ]);

               if ($request->input('token_notificacion') != '' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != 'null') {

                    /*$explode1 = explode(" ",$request->input('msg'));
                    $auxMsg = null;
                    for ($i=0; $i < count($explode1); $i++) { 
                        $auxMsg = $auxMsg.$explode1[$i].'%20'; 
                    }*/

                    // Orden del reemplazo
                    //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    // Procesa primero \r\n así no es convertido dos veces.
                    $newstr = str_replace($order, $replace, $request->input('msg'));

                    $emisor = \App\User::select('id', 'nombre', 'tipo_usuario', 'token_notificacion', 'imagen')->find($msg->emisor_id);

                    //Tratar los espacios del nombre
                    $nombre = str_replace($order, $replace, $emisor->nombre);
                    $emisor->nombre = $nombre;

                    //Tratar los & de la imagen
                    $imagen = str_replace('&', '%26', $emisor->imagen);
                    $emisor->imagen = $imagen;

                    //Tratar los espacios de la fecha del mensaje
                    $created_at = str_replace($order, $replace, $msg->created_at);
                    $msgAux = array('id'=>$msg->id, 'estado'=>$msg->estado,
                        'chat_id'=>$msg->chat_id, 'emisor_id'=>$msg->emisor_id,
                        'receptor_id'=>$msg->receptor_id, 'created_at'=>$created_at);

                    $obj = array('chat_id'=>$msg->chat_id, 'emisor'=>$emisor, 'msg'=>$msgAux);
                    $obj = json_encode($obj);

                    $oneSignal = $this->enviarNotificacionProveedor($request->input('token_notificacion'), $newstr, 'null', 2, $obj);

                    //Enviar a los demas admins
                    //Enviar a los demas admins
                    $admins = \App\User::select('token_notificacion')
                       ->where(function ($query) {
                            $query
                                ->where('tipo_usuario', 1)
                                ->orwhere('tipo_usuario', 5)
                                ->orwhere('tipo_usuario', 6);
                        })
                       ->where('ciudad', $request->input('ciudad_id'))
                       ->where('id', '<>', $request->input('emisor_id'))
                       ->get();

                       $this->enviarNotificacion($admins[0]->token_notificacion, $newstr, 'null', 2, $obj);

                }
            }

            $receptor = \App\User::select('id', 'token_notificacion')->find($msg->receptor_id);
            $msg->token_notificacion = $receptor->token_notificacion;
            //$msg->emisor = \App\User::select('id', 'nombre', 'imagen')->find($msg->emisor_id);

            return response()->json(['message'=>'Mensaje enviado con éxito.', 'chat'=>$chat, 'msg'=>$msg, 'oneSignal'=>$oneSignal, 'tipo'=>'if'], 200);
        }
        //Crear el mensaje asociado al chat
        else{

            if ($bandera) {
                $chat = $chat[0];
            }

            if ($request->input('emisor') == 'admin') {

               $msg = $chat->mensajes()->create([
                    'msg' => $request->input('msg'),
                    'emisor_id' => $chat->admin_id,
                    'receptor_id' => $chat->usuario_id,
                    'created_at' => $request->input('created_at'),
                ]);

               if ($request->input('token_notificacion') != '' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != 'null') {

                    /*$explode1 = explode(" ",$request->input('msg'));
                    $auxMsg = null;
                    for ($i=0; $i < count($explode1); $i++) { 
                        $auxMsg = $auxMsg.$explode1[$i].'%20'; 
                    }*/

                    // Orden del reemplazo
                    //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    // Procesa primero \r\n así no es convertido dos veces.
                    $newstr = str_replace($order, $replace, $request->input('msg'));

                    $emisor = \App\User::select('id', 'nombre', 'tipo_usuario', 'token_notificacion', 'imagen')->find($msg->emisor_id);

                    //Tratar los espacios del nombre
                    $nombre = str_replace($order, $replace, $emisor->nombre);
                    $emisor->nombre = $nombre;

                    //Tratar los & de la imagen
                    $imagen = str_replace('&', '%26', $emisor->imagen);
                    $emisor->imagen = $imagen;

                    //Tratar los espacios de la fecha del mensaje
                    $created_at = str_replace($order, $replace, $msg->created_at);
                    $msgAux = array('id'=>$msg->id, 'estado'=>$msg->estado,
                        'chat_id'=>$msg->chat_id, 'emisor_id'=>$msg->emisor_id,
                        'receptor_id'=>$msg->receptor_id, 'created_at'=>$created_at);

                    $obj = array('chat_id'=>$msg->chat_id, 'emisor'=>$emisor, 'msg'=>$msgAux);
                    $obj = json_encode($obj);

                    $oneSignal = $this->enviarNotificacionProveedor($request->input('token_notificacion'), $newstr, 'null', 2, $obj);

                    //Enviar a los demas admins
                    //Enviar a los demas admins
                    $admins = \App\User::select('token_notificacion')
                       ->where(function ($query) {
                            $query
                                ->where('tipo_usuario', 1)
                                ->orwhere('tipo_usuario', 5)
                                ->orwhere('tipo_usuario', 6);
                        })
                       ->where('ciudad', $request->input('ciudad_id'))
                       ->where('id', '<>', $request->input('emisor_id'))
                       ->get();

                       $this->enviarNotificacion($admins[0]->token_notificacion, $newstr, 'null', 2, $obj);

                }

            }else if ($request->input('emisor') == 'repartidor') {

               $msg = $chat->mensajes()->create([
                    'msg' => $request->input('msg'),
                    'emisor_id' => $chat->usuario_id,
                    'receptor_id' => $chat->admin_id,
                    'created_at' => $request->input('created_at'),
                ]);

               if ($request->input('token_notificacion') != '' && $request->input('token_notificacion') != null && $request->input('token_notificacion') != 'null') {

                    /*$explode1 = explode(" ",$request->input('msg'));
                    $auxMsg = null;
                    for ($i=0; $i < count($explode1); $i++) { 
                        $auxMsg = $auxMsg.$explode1[$i].'%20'; 
                    }*/

                    // Orden del reemplazo
                    //$str     = "Line 1\nLine 2\rLine 3\r\nLine 4\n";
                    $order   = array("\r\n", "\n", "\r", " ", "&");
                    $replace = array('%20', '%20', '%20', '%20', '%26');

                    // Procesa primero \r\n así no es convertido dos veces.
                    $newstr = str_replace($order, $replace, $request->input('msg'));

                    $emisor = \App\User::select('id', 'nombre', 'tipo_usuario', 'token_notificacion', 'imagen')->find($msg->emisor_id);

                    //Tratar los espacios del nombre
                    $nombre = str_replace($order, $replace, $emisor->nombre);
                    $emisor->nombre = $nombre;

                    //Tratar los & de la imagen
                    $imagen = str_replace('&', '%26', $emisor->imagen);
                    $emisor->imagen = $imagen;

                    //Tratar los espacios de la fecha del mensaje
                    $created_at = str_replace($order, $replace, $msg->created_at);
                    $msgAux = array('id'=>$msg->id, 'estado'=>$msg->estado,
                        'chat_id'=>$msg->chat_id, 'emisor_id'=>$msg->emisor_id,
                        'receptor_id'=>$msg->receptor_id, 'created_at'=>$created_at);

                    $obj = array('chat_id'=>$msg->chat_id, 'emisor'=>$emisor, 'msg'=>$msgAux);
                    $obj = json_encode($obj);

                    $oneSignal = $this->enviarNotificacionProveedor($request->input('token_notificacion'), $newstr, 'null', 2, $obj);

                    //Enviar a los demas admins
                    //Enviar a los demas admins
                    $admins = \App\User::select('token_notificacion')
                       ->where(function ($query) {
                            $query
                                ->where('tipo_usuario', 1)
                                ->orwhere('tipo_usuario', 5)
                                ->orwhere('tipo_usuario', 6);
                        })
                       ->where('ciudad', $request->input('ciudad_id'))
                       ->where('id', '<>', $request->input('emisor_id'))
                       ->get();

                       $this->enviarNotificacion($admins[0]->token_notificacion, $newstr, 'null', 2, $obj);

                }
            }

            $receptor = \App\User::select('id', 'token_notificacion')->find($msg->receptor_id);
            $msg->token_notificacion = $receptor->token_notificacion;
            //$msg->emisor = \App\User::select('id', 'nombre', 'imagen')->find($msg->emisor_id);

           return response()->json(['message'=>'Mensaje enviado con éxito.',
             'chat'=>$chat, 'msg'=>$msg, 'oneSignal'=>$oneSignal, 'tipo'=>'else'], 200);

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
        //cargar un chat
        $chat = \App\ChatRepartidor::
            with(['admin' => function ($query) {
                $query->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion');
            }])
            ->with(['usuario' => function ($query) {
                $query->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion');
            }])
            ->with(['mensajes.emisor' => function ($query) {
                $query->select('usuarios.id', 'usuarios.nombre', 'usuarios.imagen')->orderBy('id', 'asc');
            }])
            ->find($id);

        if(count($chat)==0){
            return response()->json(['error'=>'No existe el chat con id '.$id], 404);          
        }else{

            return response()->json(['chat'=>$chat], 200);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Comprobamos si el chat existe o no.
        $chat=\App\ChatRepartidor::find($id);

        if (count($chat)==0)
        {
            // Devolvemos error codigo http 404
            return response()->json(['error'=>'No existe el chat con id '.$id], 404);
        }
       
        $mensajes = $chat->mensajes;

        if (sizeof($mensajes) > 0)
        {
            for ($i=0; $i < count($mensajes) ; $i++) { 
                $mensajes[$i]->delete();
            }
        }

        // Eliminamos el chat.
        $chat->delete();

        return response()->json(['message'=>'Se ha eliminado correctamente el chat.'], 200);
    }

    /*Actualiza los mensajes de un receptor_id en un chat a leidos (estado=2)*/
    public function leerMensajes(Request $request)
    {
        DB::table('msgs_chats_repartidores')
                ->where('chat_id', $request->input('chat_id'))
                //->where('receptor_id', $request->input('receptor_id'))
                /*->where('emisor_id', $request->input('emisor_id'))*/
                ->where('estado', 1)
                ->update(['estado' => 2]);
        $rows = \App\MsgChatCliente::where('estado', 1)
            ->where('chat_id', $request->input('chat_id'))
            //->where('receptor_id', $request->input('receptor_id'))
            ->get();

        foreach($rows as $row){
            $rows->estado = 2;
            $rows->save();
        }

        return response()->json(['message'=>'ok'], 200);
    }

    public function MsgsPanelRep(Request $request)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar los ultimos 10 ids de mensajes sin leer
        $idsSinLeer = \App\MsgChatRepartidor::
            select(/*'id', 'estado', 'msg', 'created_at',*/ DB::raw('Max(id) AS max_id'))
            ->where('estado', 1)
            //->where('receptor_id', $receptor_id)
            ->groupBy('chat_id')
            ->orderBy('max_id', 'desc')
            ->take(50)
            ->get();

        $idsAux = [];
        for ($i=0; $i < count($idsSinLeer); $i++) { 
            array_push($idsAux, $idsSinLeer[$i]->max_id);
        }

        //cargar toda la info de los mensajes sin leer
        $msgs = \App\MsgChatRepartidor::select('id', 'msg', 'estado', 'chat_id', 'emisor_id', 'receptor_id', 'created_at')
            ->whereIn('id', $idsAux)
            ->with(['emisor' => function ($query)use ($zonas) {
                $query->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion','zona_id')->whereIn('zona_id',$zonas);
            }])
            ->orderBy('id', 'desc')
            ->get();

        $msgs2=[];
        for ($i=0; $i < count($msgs); $i++) { 
            for ($j=0; $j < count($zonas); $j++) { 
                if ($msgs[$i]->emisor->zona_id==$zonas[$j]) {
                    array_push($msgs2, $msgs[$i]);
                }
            }
        }
        $msgs=$msgs2;

        return response()->json([/*'idsSinLeer'=>$idsAux,*/ 'msgs'=>$msgs], 200); 
    }

    /*Retorna los ultimos 10 mensajes sin leer (estado=1) de un receptor_id*/
    public function getMsgsSinLeer(Request $request, $receptor_id)
    {
        $zonas=$this->ciudad($request->input('ciudad_id'));
        //cargar los ultimos 10 ids de mensajes sin leer
        $idsSinLeer = \App\MsgChatRepartidor::
            select(/*'id', 'estado', 'msg', 'created_at',*/ DB::raw('Max(id) AS max_id'))
            ->where('estado', 1)
            ->where('receptor_id', $receptor_id)
            ->groupBy('chat_id')
            ->orderBy('max_id', 'desc')
            ->take(50)
            ->get();

        $idsAux = [];
        for ($i=0; $i < count($idsSinLeer); $i++) { 
            array_push($idsAux, $idsSinLeer[$i]->max_id);
        }

        //cargar toda la info de los mensajes sin leer
        $msgs = \App\MsgChatRepartidor::select('id', 'msg', 'estado', 'chat_id', 'emisor_id', 'receptor_id', 'created_at')
            ->whereIn('id', $idsAux)
            ->with(['emisor' => function ($query)use ($zonas) {
                $query->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion','zona_id')->whereIn('zona_id',$zonas);
            }])
            ->orderBy('id', 'desc')
            ->get();

        $msgsAux=[];
        for ($i=0; $i < count($msgs) ; $i++) { 
            try { 
                if ($msgs[$i]->emisor!=null) {  
                                  
                    array_push($msgsAux,$msgs[$i]);
                }
            } catch (Exception $e) {
                //return response()->json(['error'=>$e], 500);
            }
        }
        $msgs=$msgsAux;

        return response()->json([/*'idsSinLeer'=>$idsAux,*/ 'msgs'=>$msgs], 200); 
    }


    /*Retorna el chat de un repartidor*/
    public function miChat(Request $request, $usuario_id)
    {
        //Cargar el chat.
        $chat=\App\ChatRepartidor::where('usuario_id', $usuario_id)->get();

        //Cargar los datos del admin
            $admin=\App\User::whereIn('tipo_usuario', [0,1,5,6])
                ->select('id', 'nombre', 'imagen', 'tipo_usuario', 'token_notificacion','ciudad','pais_id','zona_id')->with('registro')
                ->get();
            $activo=\App\Repartidor::where('usuario_id', $usuario_id)->first();
            $direccion=\App\Registro::where('usuario_id', $usuario_id)->first();

        $calificaciones = \App\Calificacion::/*where('usuario_id',$usuario_id)->*/where('califique_a',$activo->id)->with('usuario')->with('producto')->get();
            //return $calificaciones;
            if (count($calificaciones)>1) {
                $promedio=0;
                for ($j=0; $j < count($calificaciones); $j++) { 
                    $promedio+=$calificaciones[$j]->puntaje;
                }
                $promedio=round($promedio/count($calificaciones), 0, PHP_ROUND_HALF_UP); 
            }else{
              $promedio=5;  
            }

        $pedidos = \App\Pedido::where('repartidor_id',$activo->id)->where('estado',3)->get();

        if (count($chat)==0)
        {

            if (count($admin)==0) {
                // Devolvemos un código 409 Conflict.
                return response()->json(['Error'=>'No hay admis disponibles para iniciar un chat.','pedidos'=>count($pedidos),'admin'=>$admin,'activo'=>$activo->activo,'direccion'=>$direccion->direccion, 'promedio_calificacion'=>$promedio,'calificaciones'=>$calificaciones], 409);
            }
            else{
                // Devolvemos error codigo http 404
                return response()->json(['pedidos'=>count($pedidos),'admin'=>$admin,'activo'=>$activo->activo,'direccion'=>$direccion->direccion, 'promedio_calificacion'=>$promedio,'calificaciones'=>$calificaciones], 404); 
            }
            
        }

            
            

        return response()->json(['pedidos'=>count($pedidos),'chat'=>$chat[0], 'admin'=>$admin,'activo'=>$activo->activo,'direccion'=>$direccion->direccion, 'promedio_calificacion'=>$promedio,'calificaciones'=>$calificaciones], 200);
    }
}
