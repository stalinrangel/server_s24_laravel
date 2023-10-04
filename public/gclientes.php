<?php
ini_set('display_errors', 1);
$title=$_GET['titulo'];
$message=$_GET['contenido'];
$accion=$_GET['accion'];

$path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
$server_key = "AAAANlaUNVE:APA91bFoYCfIx8_8aWVwR67HDzSB4i_qoziMJ21QUx_4kZBB37HfTtMeci9h3dAKc--G3e9RBHiIol1ASLaTggh16Tpg7RmqFKBOYiXaDkbG6IPYjPtA_Fqv1vxdV3x_z1j1qKEPZ7s4";

$headers = array(
    'Authorization:key=' .$server_key,
    'Content-Type:application/json'
);

//me traigo todos los tokens
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/index_zonas?ciudad_id=1");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

$response1 = curl_exec($ch);
$response1 =json_decode($response1);
curl_close($ch);

for ($i=0; $i < count($response1); $i++) { 

    $fields = array(
        'to'=>$response1[$i],
        'notification'=>array('title'=>$title,'body'=>$message),
        'data'=>array('title'=>$title,'body'=>$message,'accion'=>$accion,'pedido_id'=>$pedido_id,'obj'=>$obj)
    );
    
    $payload = json_encode($fields);
    
    echo $payload;
    
    $curl_session = curl_init();
    curl_setopt($curl_session, CURLOPT_URL, $path_to_fcm);
    curl_setopt($curl_session, CURLOPT_POST, true);
    curl_setopt($curl_session, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($curl_session, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl_session, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl_session, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    curl_setopt($curl_session, CURLOPT_POSTFIELDS, $payload);
    $result = curl_exec($curl_session);
    echo $result;

}

?>