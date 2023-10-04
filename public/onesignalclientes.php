<?PHP


	function sendMessage(){

		$token_notificacion=$_GET['token_notificacion'];
		$contenido=$_GET['contenido'];
		$pedido_id=$_GET['pedido_id'];
		$accion=$_GET['accion'];
		//$obj=$_REQUEST["obj"];
		$obj=$_GET['obj'];
		$token_notificacion="820450ba-095c-452f-891b-658823875ace";
		print_r($_GET);
		 
			$content = array(
				"en" => $contenido
				);
			
			$fields = array(
				'app_id' => "fad93cb9-e8dc-41ca-b24f-9e17ca80784b",
				'include_player_ids' => array($token_notificacion),
	      		'data' => array("contenido" => $contenido,"pedido_id"=>$pedido_id,"accion"=>$accion, "obj"=>$obj),
				'contents' => $content,
				'android_channel_id' => 'e560827a-cf4b-4675-8e6f-bfc00d78b33f',
				'android_sound' => 'notify',
				'ios_sound'=> 'notify',
				/*,
				'small_icon'=>'http://shopper.internow.com.mx/assets/img/logo-symbol.png',
				'adm_small_icon'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'chrome_web_icon'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'chrome_web_image'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'firefox_icon'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'chrome_icon'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'chrome_web_default_notification_icon'=>'http://mouvers.mx/terminos/imgs/mouver.png',
				'onesignal_bgimage_default_image'=>'http://mouvers.mx/terminos/imgs/mouver.png'*/
			);
			
			$fields = json_encode($fields);
	    	print("\nJSON sent:\n");
	    	print($fields);
			
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8',
														'Authorization: Basic MDNkOGZlNmMtYzlhZC00MWIzLWFlNDktOTQyOGQzMDJhYWU3'));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_POST, TRUE);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response = curl_exec($ch);
			curl_close($ch);
			$pushes=$pushes.'"'.$server_output[$i]->push.'",';

		$pushes=substr($pushes, 0,-1);
		echo $pushes;
		
		return $response;
	}
	
	$response = sendMessage();
	$return["allresponses"] = $response;
	$return = json_encode( $return);
	
  print("\n\nJSON received:\n");
	print($return);
  print("\n");
?>