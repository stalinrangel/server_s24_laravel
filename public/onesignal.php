<?PHP


	function sendMessage(){

		$token_notificacion=$_GET['token_notificacion'];
		$contenido=$_GET['contenido'];
		$pedido_id=$_GET['pedido_id'];
		$accion=$_GET['accion'];
		//$obj=$_REQUEST["obj"];
		$obj=$_GET['obj'];

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/index_zonasadmin?ciudad_id=1");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

		$response1 = curl_exec($ch);
		$response1 =json_decode($response1);
		curl_close($ch);


		$token_notificacion=$response1[0];
		print_r($_GET);
		 
			$content = array(
				"en" => $contenido
				);
			
			$fields = array(
				'app_id' => "d972ea38-fbba-48de-ac2c-991904917c41",
				'include_player_ids' => array($token_notificacion),
	      		'data' => array("contenido" => $contenido,"pedido_id"=>$pedido_id,"accion"=>$accion, "obj"=>$obj),
				'contents' => $content,
				'android_channel_id' => 'd3180c9d-44fc-4384-a49b-dd4b10609ad9',
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
														'Authorization: Basic NGMxNWE5YTItNjM2OC00NGNlLWE0NTYtYzNlNzg3NGI3OWNm'));
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