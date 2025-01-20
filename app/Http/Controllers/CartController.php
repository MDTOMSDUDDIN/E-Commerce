<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index(){
        $items=Cart::instance('cart')->content();
        return view('cart',['items'=>$items,]);
    }


    public function add_to_cart(Request $request){
            Cart::instance('cart')->add($request->id,$request->name,$request->quantity,$request->price)->associate('App\Models\Product');
    
            return redirect()->back();
        }
    
    public function increase_cart_quantity($rowId){
        $product=Cart::instance('cart')->get($rowId);
        $qty=$product->qty + 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }
    
    public function decrease_cart_quantity($rowId){
        $product=Cart::instance('cart')->get($rowId);
        $qty=$product->qty - 1;
        Cart::instance('cart')->update($rowId,$qty);
        return redirect()->back();
    }

    public function remove_item($rowId){
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }

    public function empty_cart(){
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }

    public function apply_coupon_code(Request $request){
        $coupon_code =$request->coupon_code;
        if(isset($coupon_code)){
            $coupon= Coupon::where('code',$coupon_code)->where('expiry_date','>=',Carbon::today())
            ->where('cart_value','<=',Cart::instance('cart')->subtotal())->first();

            if(!$coupon){
                return redirect()->back()->with('error','Invalid Coupon Code !');
            }else{
                Session::put('coupon',[
                    'code'=>$coupon->code,
                    'type'=>$coupon->type,
                    'value'=>$coupon->value,
                    'cart_value'=>$coupon->cart_value,
                ]);
                $this->calculateDiscount();
                return redirect()->back()->with('success','Coupon has been Applied !');
            }
        }else{
            return redirect()->back()->with('error','Invalid Coupon Code !!');
        }
    }

    public function calculateDiscount(){
        $discount=0;
        if(Session::has('coupon')){
            if(Session::get('coupon')['type']=='fixed'){
                $discount=Session::get('coupon')['value'];
            }else{
                $discount=(Cart::instance('cart')->subtotal()*Session::get('coupon')['value'])/100;
            }
            
            $subtoalAfterDiscount=Cart::instance('cart')->subtotal() - $discount;
            $taxAfterDiscount=($subtoalAfterDiscount * config('cart.tax'))/100;
            $totalAfterDiscount= $subtoalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts',[
                'discount'=> number_format(floatval($discount),2,'.',''),
                'subtotal'=> number_format(floatval($subtoalAfterDiscount),2,'.',''),
                'tax'=> number_format(floatval($taxAfterDiscount),2,'.',''),
                'tatal'=> number_format(floatval($totalAfterDiscount),2,'.','')
            ]);
        }
    }
    
}
