<?php

namespace App\Http\Controllers\ViewTicket;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class ViewTicketController extends Controller
{
    public function index($order_id)
    {
        $order = DB::table('sale_order')
            ->where('sale_or_no', '=', $order_id)
            ->first();

        $productId = $order->product_id;

        if ($productId == env('PRODUCT_SJT'))
        {
            return Inertia::render('View', [
                'type' => $productId,
                'order_id' => $order_id,
                'upwardTicket' => $this->getSjtTrips($order_id)
            ]);
        }
        else
        {


            return Inertia::render('View', [
                'type' => $productId,
                'order_id' => $order_id,
                'upwardTicket' => $this->getRjtTrips($order_id, env('OUTWARD')),
                'returnTicket' => $this->getRjtTrips($order_id, env('RETURN'))

            ]);
        }
    }

    private function getSjtTrips($order_id)
    {
        return DB::table('sjt_sl_booking as sjt')
            ->join('sale_order as so', 'so.sale_or_id', 'sjt.sale_or_id')
            ->join('stations as s', 's.id', 'so.src_stn_id')
            ->join('stations as d', 'd.id', 'so.des_stn_id')
            ->where('so.sale_or_no', '=', $order_id)
            ->select(['so.*', 's.stn_name as source', 'd.stn_name as destination', 'sjt.*'])
            ->get();

    }

    private function getRjtTrips($order_id, $dir)
    {
        return DB::table('rjt_sl_booking as rjt')
            ->join('sale_order as so', 'so.sale_or_id', 'rjt.sale_or_id')
            ->join('stations as s', 's.id', 'so.src_stn_id')
            ->join('stations as d', 'd.id', 'so.des_stn_id')
            ->where('so.sale_or_no', '=', $order_id)
            ->where('rjt.qr_dir', '=', $dir)
            ->select(['so.*', 's.stn_name as source', 'd.stn_name as destination', 'rjt.*'])
            ->get();
    }

    public function send_tkt($mobile_no,$order_id){
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
            CURLOPT_POSTFIELDS => "token=kh3213l8sbr5uo9n&to=".$mobile_no."&body= Happy Journey,\n\nClick on the below link to view your ticket\n\nhttps://mmopl-wa.atekpayments.com/ticket/view/".$order_id."&priority=1&referenceId=",
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

}
