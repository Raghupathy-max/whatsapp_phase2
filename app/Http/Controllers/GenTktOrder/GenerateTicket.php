<?php

namespace App\Http\Controllers\GenTktOrder;

use App\Http\Controllers\Api\MMOPL\ApiController;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class GenerateTicket extends Controller
{

    public function index($id){
        return Inertia::render('Booking',[
            'stations' => DB::table('stations')->get(['stn_id', 'stn_name']),
            'token'    => $id
        ]);
    }




    //Generate Order
    public function genOrder(Request $request){
        $data =  DB::table('sale_order')
            ->where('sale_or_no','=',$request->input('sale_or_no'))
            ->first();

        if(is_null($data)){
            return response([
                'status' => false,
                'message'=> 'token not found'
            ]);

        }else{

            DB::table('sale_order')
                ->where('sale_or_no','=',$request->input('sale_or_no'))
                ->update([
                    'src_stn_id'   =>$request->input('src_stn_id'),
                    'des_stn_id'   =>$request->input('des_stn_id'),
                    'unit'         => $request->input('unit'),
                    'unit_price'   => $request->input('unit_price'),
                    'total_price'  => $request->input('total_price'),
                    'product_id'   => $request->input('product_id'),
                    'pass_id'      => $request->input('pass_id')
                ]);


            return response([
                'status'     => true,
                'message'    => 'updated successfully',
                'sale_or_no' => $request->input('sale_or_no')
            ]);

        }

    }

    //Generate Ticket
    public function genTicket(Request $request){

        $data = DB::table('sale_order as so')
            ->join('atek_user_data as aud','aud.atek_token','=', 'so.sale_or_no')
            ->where('so.sale_or_no','=',$request->input('sale_or_no'))
            ->first();


        if(is_null($data)){

            return response([
                'status' =>false,
                'message'=> 'invalid token'
            ]);

        }else{

            $api = new ApiController();
            $response = $api->genSjtRjtTicket($data);

            $epoch = $response->data->masterExpiry;
            $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
            $MasterExpiry = $dt->format('Y-m-d H:i:s');

            DB::table('sale_order')
                ->where('sale_or_no','=',$request->input('sale_or_no'))
                ->update([
                    'mm_ms_acc_id'  => $response->data->transactionId,
                    'ms_qr_no'      =>  $response->data->masterTxnId,
                    'ms_qr_exp'     => $MasterExpiry,
                    'sale_or_status'=>true
                ]);

            $time = Carbon::now();
            $currentTime = $time->toDateTimeString();
            $trips = $response->data->trips;

            if($response->data->qrType == env('PRODUCT_SJT')){
                foreach ($trips as $trip){
                    $epoch = $trip->expiryTime;
                    $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
                    $SlaveExpiry = $dt->format('Y-m-d H:i:s');

                    DB::table('sjt_sl_booking')->insert([
                        'sale_or_id'    => $data ->sale_or_id,
                        'mm_ms_acc_id'  =>  $response->data->transactionId,
                        'mm_sl_acc_id'  => $trip->transactionId,
                        'sl_qr_no'      => $trip->qrCodeId,
                        'sl_qr_exp'     => $SlaveExpiry,
                        'qr_dir'        => env('QR_DIR'),
                        'qr_data'       => $trip->qrCodeData,
                        'txn_date'      => $currentTime,

                    ]);
                }
            }else{
                foreach ($trips as $trip){
                    $epoch = $trip->expiryTime;
                    $dt = new DateTime("@$epoch");  // convert UNIX timestamp to PHP DateTime
                    $SlaveExpiry = $dt->format('Y-m-d H:i:s');

                    DB::table('rjt_sl_booking')->insert([

                        'sale_or_id'    => $data ->sale_or_id,
                        'mm_ms_acc_id'  =>  $response->data->transactionId,
                        'mm_sl_acc_id'  => $trip->transactionId,
                        'sl_qr_no'      => $trip->qrCodeId,
                        'sl_qr_exp'     => $SlaveExpiry,
                        'qr_dir'        => $trip->type == "OUTWARD" ? 1 : 2,
                        'qr_data'       => $trip->qrCodeData,
                        'txn_date'      => $currentTime
                    ]);
                }
            }


            return response([
                'status'  => true,
                'message' => 'Ticket generated successfully',
                'response'=> $response
            ]);

        }

    }

    public function viewTicket(Request $request){
        $data =  DB::table('sale_order')
            ->where('sale_or_no','=',$request->input('sale_or_no'))
            ->first();

        if(is_null($data)){
            return response([
                'status' => false,
                'message' => 'Order Invalid'
            ]);
        }else{
            if($data->product_id == env('PRODUCT_SJT')){
                $ticket_data =  DB::table('sale_order')

                    ->join('sjt_sl_booking','sjt_sl_booking.mm_ms_acc_id','=','sale_order.mm_ms_acc_id')
                    ->where('sale_or_no','=',$request->input('sale_or_no'))
                    ->get();
                return response([
                    'status' => true,
                    'data' => $ticket_data
                ]);
            }else{
                $ticket_data =  DB::table('sale_order')

                    ->join('rjt_sl_booking','rjt_sl_booking.mm_ms_acc_id','=','sale_order.mm_ms_acc_id')
                    ->where('sale_or_no','=',$request->input('sale_or_no'))
                    ->get();
                return response([
                    'status' => true,
                    'data' => $ticket_data
                ]);
            }

        }

    }


}
