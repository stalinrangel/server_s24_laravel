<?php

$token=$_GET['token_notificacion'];
$title=$_GET['titulo'];
$message=$_GET['contenido'];
$accion=$_GET['accion'];
$pedido_id=$_GET['pedido_id'];
$obj=$_GET['obj'];

$path_to_fcm = 'https://fcm.googleapis.com/fcm/send';
$server_key = "AAAANlaUNVE:APA91bFoYCfIx8_8aWVwR67HDzSB4i_qoziMJ21QUx_4kZBB37HfTtMeci9h3dAKc--G3e9RBHiIol1ASLaTggh16Tpg7RmqFKBOYiXaDkbG6IPYjPtA_Fqv1vxdV3x_z1j1qKEPZ7s4";

//$deviceToken = "e57W64lxTxano8NjytB0Q8:APA91bEkeXYTTWYH1J-1ewzdOK3nUDqvL0NIX09pOm8qNuFD4WhGrM7gFNIF2cvaObaIwFC9NRspIg1KHj-vNCYYv5kr8zE--0bia1uvooUhuugAK_xX8v6u4idBqRMjBiqk6_n37-5h";
$deviceToken =$token;
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