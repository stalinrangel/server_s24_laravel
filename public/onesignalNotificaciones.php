<?PHP


	function sendMessage(){

		$token_notificacion=$_GET['token_notificacion'];
		$contenido=$_GET['contenido'];
		$ciudad_id=$_GET['ciudad_id'];
		$pedido_id=$_GET['pedido_id'];
		$accion=$_GET['accion'];
		//$obj=$_REQUEST["obj"];
		$obj=$_GET['obj'];
		
		print_r($_GET);
		//return $ciudad_id;
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://service24.app/apii/public/index_zonasp?ciudad_id=".$ciudad_id);
			//curl_setopt($ch, CURLOPT_URL, "http://localhost/apii/public/index_zonasp?ciudad_id=".$ciudad_id);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			//curl_setopt($ch, CURLOPT_POST, TRUE);
			//curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

			$response1 = curl_exec($ch);
		 	$response1 =json_decode($response1);
		 	curl_close($ch);
		 	print('1');
		 	print($response);
		 	//return $response;

		 
			$content = array(
				"en" => $contenido
				);

			$tokes=[];
			$j=0;
			/*for ($i=0; $i < count($response1); $i++) { 
				$fields = array(
						'app_id' => "fad93cb9-e8dc-41ca-b24f-9e17ca80784b",
						'include_player_ids' => $response1[$i],
						//'included_segments' => array('All'),
			      		'data' => array("contenido" => $contenido,"pedido_id"=>$pedido_id,"accion"=>$accion, "obj"=>$obj),
						'contents' => $content,
						'android_channel_id' => 'e560827a-cf4b-4675-8e6f-bfc00d78b33f',
						'android_sound' => 'notify',
						'ios_sound'=> 'notify',
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
			}*/

			if (count($response1)<=10) {
					$fields = array(
						'app_id' => "fad93cb9-e8dc-41ca-b24f-9e17ca80784b",
						'include_player_ids' => $response1,
						//'included_segments' => array('All'),
			      		'data' => array("contenido" => $contenido,"pedido_id"=>$pedido_id,"accion"=>$accion, "obj"=>$obj),
						'contents' => $content,
						'android_channel_id' => 'e560827a-cf4b-4675-8e6f-bfc00d78b33f',
						'android_sound' => 'notify',
						'ios_sound'=> 'notify',
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
			}else if(count($response1)>10){
				for ($i=0; $i < count($response1); $i++) { 
					$j=$j+1;
					//print($j);
					array_push($tokes,$response1[$i]);
					if ($j==10) {
						print($i);

						$fields = array(
						'app_id' => "fad93cb9-e8dc-41ca-b24f-9e17ca80784b",
						'include_player_ids' => $tokes,
						//'included_segments' => array('All'),
			      		'data' => array("contenido" => $contenido,"pedido_id"=>$pedido_id,"accion"=>$accion, "obj"=>$obj),
						'contents' => $content,
						'android_channel_id' => 'e560827a-cf4b-4675-8e6f-bfc00d78b33f',
						'android_sound' => 'notify',
						'ios_sound'=> 'notify',
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
						$j=0;
						$tokes=[];
					}
					
				}
			}
		
		return $response;
	}
	
	$response = sendMessage();
	$return["allresponses"] = $response;
	$return = json_encode( $return);
	
  print("\n\nJSON received:\n");
	print($return);
  print("\n");
?>