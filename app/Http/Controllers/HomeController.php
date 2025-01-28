<?php

namespace App\Http\Controllers;

use App\Models\category;
use App\Models\Contact;
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

    public function contact_store(Request $request){
        $request->validate([
            'name'=>'required|max:100',
            'email'=>'required|email',
            'phone'=>'required|numeric|digits:11',
            'comment'=>'required',
        ]);

        $contact=new Contact();
        $contact->name=$request->name;
        $contact->email=$request->email;
        $contact->phone=$request->phone;
        $contact->comment=$request->comment;
        $contact->save();
        return redirect()->back()->with('success','Your Message Has been Sending Successfully ');
    }

    public function search(Request $request){
        $query=$request->input('query');
        $result=Product::where('name','LIKE',"%$query%")->get()->take(8);
        return response()->json($result);
    }
}
