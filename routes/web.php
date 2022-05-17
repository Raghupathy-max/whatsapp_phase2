<?php

use Illuminate\Support\Facades\Route;

Route::get('/index/{token}',[\App\Http\Controllers\IndexController::class,'indexPage']);
Route::get('/gen/order/{id}',[\App\Http\Controllers\GenTktOrder\GenerateTicket::class,'index']);
Route::get('payment/{id}',[\App\Http\Controllers\Payment\PaymentController::class,'payPage']);

Route::get('ticket/view/{order_id}', [\App\Http\Controllers\ViewTicket\ViewTicketController::class, 'index'])->name('ticket.view');
