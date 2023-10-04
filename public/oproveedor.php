<?php
$token=$_GET['token_notificacion'];
$title=$_GET['titulo'];
$message=$_GET['contenido'];
$accion=$_GET['accion'];
$pedido_id=$_GET['pedido_id'];
$obj=$_GET['obj'];

$path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
$server_key = "AAAA5xT4gVQ:APA91bHK3FZx07Aplfwurq518epbY1qIwe88Ly3uxW1MZaH-ke7AIdKxg9cBkXzrcLVv1_bWt6WqND0M9eKsmaDDQPYcxAJLmh5CAEuGHXx5wGQKLt2E6h6cl0i_YsYfZBUr4ri1cenG";

$deviceToken = $token;
$headers = array(
    'Authorization:key=' .$server_key,
    'Content-Type:application/json'
);

$fields = array('to'=>$deviceToken,
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

?>