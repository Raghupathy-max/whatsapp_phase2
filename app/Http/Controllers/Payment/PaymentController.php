<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaymentController extends Controller
{
    public function payPage($id){
        return Inertia::render('Payment',[
           'token' => $id
        ]);
    }
}
