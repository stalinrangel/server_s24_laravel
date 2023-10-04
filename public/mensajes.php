<?php

// Required if your environment does not handle autoloading
require __DIR__ . '../vendor/autoload.php';

print_r('2');
return 1;
// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;

// Your Account SID and Auth Token from twilio.com/console
$sid = 'ACf5be9d98c1dba8b32f4805c1c9d2c3b6';
$token = '9c4f4707e97cedd3d3ca7185cc369811';
$client = new Client($sid, $token);

// Use the client to do fun stuff like send text messages!
$client->messages->create(
    // the number \App\User::you'd like to send the message to
    '+12029465030',
    [
        // A Twilio phone number you purchased at twilio.com/console
        'from' => '+584147428420',
        // the body of the text message you'd like to send
        'body' => 'Hey Jenny! Good luck on the bar exam!'
    ]
);