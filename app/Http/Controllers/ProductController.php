<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function products(){
         $products=Product::orderBy('created_at','desc')->paginate(10);
         return  view('admin.products',[
            'products'=>$products,
         ]);
    }
}
