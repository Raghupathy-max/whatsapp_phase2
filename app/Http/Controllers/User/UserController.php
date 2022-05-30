<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\SmsNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function inbound(){
        $data = file_get_contents("php://input");
        $event = json_decode($data, true);
        if(isset($event)) {
            //Here, you now have event and can process them how you like e.g Add to the database or generate a response
            $file = 'log.txt';
            $data = json_encode($event) . "\n";

            $from = $event['data']['from'];
            $msg = $event['data']['body'];
            // $this->send_msg($from, $msg);
            $this->insertUser($from);

            file_put_contents($file, $data, FILE_APPEND | LOCK_EX);

        }


    }







    //Inserting User Data
    public function insertUser($mobile)
    {



        $time = Carbon::now();
        $currentTime = $time->toDateTimeString();
        $endTime = $time->addMinutes(50);
        $sessionToken = Str::random(5);
         $dbmobile = substr($mobile, 0, -5);

        DB::table('users')->insert([
            'cust_name' => "Bhaskar",
            'cust_mobile' => $dbmobile,           //$dbmobile,
            'whatsapp_no' => $mobile,
            'cust_email' => "bhaskarshukla@gmail.com",
            'session_token' => $sessionToken,
            'session_created_at' => $currentTime,
            'session_expire_at' => $endTime
        ]);

           $this->send_msg($mobile, $sessionToken);


        return response([
            'status' => true,
            'session_id' => $sessionToken,


        ]);

    }

    //Fetching User Data
    public function getUserData(Request $request)
    {


        $time = Carbon::now();
        $currentTime = $time->toDateTimeString();

        $user_data = DB::table('users')
            ->where('session_token', '=', $request->input('session_token'))
            ->first();


        if (is_null($user_data)) {
            return response([
                'status' => false,
                'error' => 'Invalid session_id',
            ]);

        } else {

            if ($currentTime >= $user_data->session_expire_at) {
                return response([
                    'status' => false,
                    'error' => 'Session expired'
                ]);
            } else {

                return response([
                    'status' => true,
                    'user_data' => $user_data
                ]);
            }

        }


    }

    public function send_msg ($mobile_no, $token){
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.ultramsg.com/instance7585/messages/chat",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "token=kh3213l8sbr5uo9n&to=".$mobile_no."&body= Hi,\nWelcome to Mumbai Metro One,\n\nClick below link to book your ticket\n\nhttps://mmopl-wa.atekpayments.com/index/".$token."&priority=1&referenceId=",
            CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            echo $response;
        }



    }



    /*public function sendMessagewhatsapp($cust_mobile, $token)
    {
        Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic OGM0Njc1M2U6cDFSdU5idFFRZ0MxWmlhNA=='
        ])
            ->withBody('{
                "from": "14157386102",
                "to": "91' . $cust_mobile . '",
                "message_type": "text",
                "text": url("Hi, Welcome to Mumbai Metro One. Please click the link www.rtfsolutions.tech/index/' . $token . '"),
                "channel": "whatsapp"
              }', 'application/json')
            ->post('https://messages-sandbox.nexmo.com/v1/messages')
            ->collect();

        return response([
            'status' => true,
            'message' => "Sended Successfully"
        ]);
    }*/
}
