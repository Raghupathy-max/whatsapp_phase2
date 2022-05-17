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
}
