<?php

use App\Http\Controllers\Api\MMOPL\FareController;

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

Route::post('/get/fare', [FareController::class, 'getFare'])->name('fare');

Route::post('/user/insert',[\App\Http\Controllers\User\UserController::class,'insertUser']);

Route::get('/get/user/',[\App\Http\Controllers\User\UserController::class,'getUserData']);

Route::post('/atek/insert',[\App\Http\Controllers\SaleOrder\SaleOrderController::class,'tokenGen']);

Route::post('/gen/order',[\App\Http\Controllers\GenTktOrder\GenerateTicket::class,'genOrder']);

Route::post('/gen/ticket',[\App\Http\Controllers\GenTktOrder\GenerateTicket::class,'genTicket']);

Route::post('/message',[\App\Http\Controllers\User\UserController::class,'sendMessagewhatsapp']);

Route::post('/webhooks/inbound',[\App\Http\Controllers\User\UserController::class,'inbound']);
