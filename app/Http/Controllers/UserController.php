<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   function index(){
    return view('user.index');
   }


   public function orders(){
      $orders=Order::where('user_id',Auth::user()->id)->orderBy('created_at','DESC')->paginate(10);
      return view('user.orders',[
         'orders'=>$orders,
      ]);
   }


  
}
