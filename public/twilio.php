<?php

require __DIR__ . '../vendor/autoload.php';
use Twilio\Rest\Client;

$sid= $_ENV["ACf5be9d98c1dba8b32f4805c1c9d2c3b6"];
$token= $_ENV["3480f4b1e1d0cf9f00c92cb75ff88e79"];
/*
$client=new Client($sid,$token);

$client->messages->create(
	$_ENV["+12029465030"],
	array(
		'form'=>'+584147428420',
		'body'=>'Prueba Ser24'
	)
);
*/
?>