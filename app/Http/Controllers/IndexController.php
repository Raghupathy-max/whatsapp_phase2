<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class IndexController extends Controller
{
    public function indexPage($token){
        return Inertia::render('Index',[
            'token'=>$token
        ]);
    }
}
