<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\Product;
use App\Models\Slide;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    function index(){
        $slides=Slide::where('status',1)->get()->take(3);
        $categories=category::orderBy('name')->get();
        $sproducts=Product::whereNotNull('sale_price')->where('sale_price','<>','')->inRandomOrder()->get()->take(8);
        $fproducts=Product::where('featured',1)->get()->take(8);
        return view('index',[
            'slides'=>$slides,
            'categories'=>$categories,
            'sproducts'=>$sproducts,
            'fproducts'=>$fproducts,
        ]);
    }

    public function contact(){
        return view('contact');
    }
}
