<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Twilio\Rest\Client;
use Validator;

use App\Http\Controllers\Controller;

class BulkSmsController extends Controller
{
    public function sendSms( Request $request )
    {
        
       // Your Account SID and Auth Token from twilio.com/console
       $sid    = env( 'TWILIO_SID' );
       $token  = env( 'TWILIO_TOKEN' );
       $client = new Client( $sid, $token );
       return 1;
       $validator = Validator::make($request->all(), [
           'numbers' => '+584121280290',
           'message' => 'prueba'
       ]);

       if ( $validator->passes() ) {

           $numbers_in_arrays = explode( ',' , $request->input( 'numbers' ) );

           $message = $request->input( 'message' );
           $count = 0;

           foreach( $numbers_in_arrays as $number )
           {
               $count++;

               $client->messages->create(
                   $number,
                   [
                       'from' => '+12029465030',
                       'body' => $message,
                   ]
               );
           }

           return back()->with( 'success', $count . " messages sent!" );
              
       } else {
           return back()->withErrors( $validator );
       }
   }
    
}
