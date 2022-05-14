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
    //Inserting User Data
    public function insertUser(Request $request)
    {
        $request->validate([
            'cust_mobile' => 'required|min:10|max:10',
            'cust_name' => 'required'
        ]);


        $time = Carbon::now();
        $currentTime = $time->toDateTimeString();
        $endTime = $time->addMinutes(50);
        $sessionToken = Str::random(30);


        DB::table('users')->insert([
            'cust_name' => $request->input('cust_name'),
            'cust_mobile' => $request->input('cust_mobile'),
            'cust_email' => $request->input('cust_email'),
            'session_token' => $sessionToken,
            'session_created_at' => $currentTime,
            'session_expire_at' => $endTime
        ]);

        $api = new UserController();
        $res = $api->sendMessagewhatsapp($request->input('cust_mobile'), $sessionToken);


        return response([
            'status' => true,
            'session_id' => $sessionToken
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


    public function sendMessage(Request $request)
    {
        $url = "https://messages-sandbox.nexmo.com/v1/messages";
        $params = ["to" => ["type" => "whatsapp", "number" => $request->input('number')],
            "from" => ["type" => "whatsapp", "number" => "14157386170"],
            "message" => [
                "content" => [
                    "type" => "text",
                    "text" => "Hello From Raghu and Laravel"
                ]
            ]

        ];
        $headers = ["Authorization" => "Basic" . base64_encode(env('NEXMO_KEY') . ":" . env('NEXMO_SECRET'))];
        $client = new \GuzzleHttp\Client();
        $response = $client->request('POST', $url, ["headers" => $headers, "json" => $params]);
        $data = $response->getBody();
        Log::Info($data);

        return response([
            'status' => true,
            'Message' => "Message sended successfully"
        ]);

    }


    public function sendMessagewhatsapp($cust_mobile, $token)
    {
        Http::withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Basic OGM0Njc1M2U6cDFSdU5idFFRZ0MxWmlhNA=='
        ])
            ->withBody('{
                "from": "14157386102",
                "to": "91' . $cust_mobile . '",
                "message_type": "text",
                "text": "Hi, Welcome to Mumbai Metro One. Please click the link www.rtfsolutions.tech/index/' . $token . '",
                "channel": "whatsapp"
              }', 'application/json')
            ->post('https://messages-sandbox.nexmo.com/v1/messages')
            ->collect();

        return response([
            'status' => true,
            'message' => "Sended Successfully"
        ]);
    }
}
