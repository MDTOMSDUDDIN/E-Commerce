<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index(){
        $slides=Slide::where('status',1)->get()->take(3);
        $categories=category::orderBy('name')->get();
        return view('index',[
            'slides'=>$slides,
            'categories'=>$categories,
        ]);
    }
}
