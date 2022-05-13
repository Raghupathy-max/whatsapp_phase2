<?php

namespace App\Http\Controllers\SaleOrder;

use App\Http\Controllers\Controller;
use App\Models\AtekUserData;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleOrderController extends Controller
{
    //Generate Atek Token
    public function tokenGen(Request $request){
        $request -> validate([
           'session_token' => 'required'
        ]);
        $randomString   = Str::random(20);
        $time           = Carbon::now();
        $currentTime    = $time->toDateTimeString();
        $endTime        = $time->addMinutes(50);


        $user_data = DB::table('users')
            ->where('session_token','=',$request->input('session_token'))
            ->first();

        if(is_null($user_data)){

            return response([
                'status' => false,
                'error'  => 'Invalid session_id',
            ]);

        }else{

            if($currentTime>= $user_data->session_expire_at){

                return response([
                    'status' => false,
                    'error'  => 'Session expired'
                ]);

            }else{

                $saleOrderNo = "ATEK" . $user_data->cust_id . strtoupper(dechex($user_data->cust_mobile + time()));
                DB::table('sale_order')->insert([
                    'sale_or_no'        => $saleOrderNo,
                   'txn_date'           =>  $currentTime,
                    'cust_id'           => $user_data->cust_id,
                    'session_token'     => $user_data->session_token,
                    'token_expire_at'   => $endTime,
                    'op_type_id'        => env('OPERATOR_ID'),
                    'media_type_id'     => env('MEDIA_TYPE_ID_MOBILE'),
                    'pg_txn_no'         => env('PG_TXN_NO')
                ]);

               AtekUserData::insert([
                   'cust_id'            => $user_data->cust_id,
                   'cust_name'          => $user_data->cust_name,
                   'cust_mobile'        => $user_data->cust_mobile,
                   'cust_email'         => $user_data->cust_email,
                    'atek_token'        => $saleOrderNo,
                    'token_created_at'  => $currentTime,
                    'token_expire_at'   => $endTime
                ]);

              /*  $data =  new AtekUserData();
                $data -> cust_id = $user_data->cust_id;
                $data -> cust_name = $user_data->cust_name;
                $data -> cust_mobile = $user_data->cust_mobile;
                $data -> cust_email = $user_data->cust_email;
                $data -> atek_token = $saleOrderNo;
                $data -> token_created_at = $currentTime;
                $data -> token_expire_at = $endTime;
                $data->save();*/


                return response([
                   'status'         => true,
                   'Message'        => 'Atek Token generated successfully',
                    'sale_or_no'    => $saleOrderNo
                ]);

            }

        }

    }


}
