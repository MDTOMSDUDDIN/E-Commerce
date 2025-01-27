<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Slide;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;


class AdminController extends Controller
{
    function index(){
        return view('admin.index');
    }


    public function brands(){
        $brands =Brand::orderBy('id','desc')->paginate(10);
        return view('admin.brands', compact('brands'));
    }

    public function add_brand(){
        return view('admin.brand-add');
    }

    public function brand_store(Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:brands,slug',
            'image'=>'mimes:png,jpg,jpeg|max:2024',
        ]);

        $brand=new Brand();
        $brand->name=$request->name;
        $brand->slug=Str::slug($request->slug);
        $image=$request->file('image');
        $file_extension=$request->file('image')->extension();
        $file_name=Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateBrandThumnailsImage($image,$file_name);
        $brand->image=$file_name;
        $brand->save();

        return redirect(route('admin.brands'))->with('status','Brands has been Add Successfully ');
    }
   
     public function GenerateBrandThumnailsImage($image,$imageName){
        $destinationPath=public_path('uploads\brands');
        $img=Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constrant){
            $constrant->aspectRadio();
        })->save( $destinationPath.'/'.$imageName);
     }
       
// Edit Brand section 
     public function edit_brand($id){
        $brands=Brand::find($id);
        return view('admin.brand-edit',[
            'brands'=>$brands,
        ]);
     }

//Update Brand section 
    public function update_brand(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = $request->slug;
        if($request->hasFile('image'))
        {            
            if (File::exists(public_path('uploads/brands').'/'.$brand->image)) {
                File::delete(public_path('uploads/brands').'/'.$brand->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateBrandThumnailsImage($image,$file_name);
            $brand->image = $file_name;
        }        
        $brand->save();        
        return redirect()->route('admin.brands')->with('status','Brand has been updated successfully !');
    }
//delete Admin Brands 
    public function delete_brand($id){
        $brand=Brand::find($id);

        if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
          File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Brands has been Delete successfully !');
    }
// //categories section starting ____________________________________________________________________________________________

    public function categories(){
        $categories=category::orderby('id','desc')->paginate();

        return view("admin.categories", [
            'categories'=>$categories,
        ]);
    }

    public function category_add(){
        return view('admin.category-add');
    }

    public function category_store(Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:categories,slug',
            'image'=>'mimes:png,jpg,jpeg|max:2024',
        ]);

        $category=new category();
        $category->name=$request->name;
        $category->slug=Str::slug($request->slug);
        $image=$request->file('image');
        $file_extension=$request->file('image')->extension();
        $file_name=Carbon::now()->timestamp.'.'.$file_extension;
        $this->GenerateCategoryThumnailsImage($image,$file_name);
        $category->image=$file_name;
        $category->save();

        return redirect(route('admin.categories'))->with('status','Category has been Add Successfully ');
    }
   
     public function GenerateCategoryThumnailsImage($image,$imageName){
        $destinationPath=public_path('uploads/categories');
        $img=Image::read($image->path());
        $img->cover(124,124,"top");
        $img->resize(124,124,function($constrant){
            $constrant->aspectRadio();
        })->save( $destinationPath.'/'.$imageName);
     }

     public function category_edit($id){
        $category=category::find($id);
        return view('admin.category-edit',[
            'category'=>$category,
        ]);
     }

     public function update_category(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug,'.$request->id,
            'image' => 'mimes:png,jpg,jpeg|max:2048'
        ]);
        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = $request->slug;
        if($request->hasFile('image'))
        {            
            if (File::exists(public_path('uploads/categories').'/'.$category->image)) {
                File::delete(public_path('uploads/categories').'/'.$category->image);
            }
            $image = $request->file('image');
            $file_extention = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp . '.' . $file_extention;
            $this->GenerateCategoryThumnailsImage($image,$file_name);
            $category->image = $file_name;
        }        
        $category->save();        
        return redirect()->route('admin.categories')->with('status','Category has been updated successfully !');
     }

     public function delete_category($id){
        $category=Category::find($id);
        if(File::exists(public_path('uploads/categories').'/'.$category->image)){
            File::delete(public_path('uploads/categories').'/'.$category->image);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status','Category has been Delete successfully !');
     }

     public function coupon(){
        $coupons=Coupon::orderBy('expiry_date','desc')->paginate(12);
        return view('admin.coupons',[
            'coupons'=>$coupons,
        ]);
     }

     public function coupon_add(){
        return view('admin.coupon-add');
     }

     public function coupon_store(Request $request){
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date',
        ]);
        $coupon=new Coupon();
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expiry_date=$request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupon')->with('status','Coupon has been Add Successfully !');
     }

     public function coupon_edit($id){
        $coupon=Coupon::find($id);
        return view('admin.coupon-edit',[
            'coupon'=>$coupon,
        ]);
     }

     public function coupon_update(Request $request){
        $request->validate([
            'code'=>'required',
            'type'=>'required',
            'value'=>'required|numeric',
            'cart_value'=>'required|numeric',
            'expiry_date'=>'required|date',
        ]);

        $coupon=Coupon::find($request->id);
        $coupon->code=$request->code;
        $coupon->type=$request->type;
        $coupon->value=$request->value;
        $coupon->cart_value=$request->cart_value;
        $coupon->expiry_date=$request->expiry_date;
        $coupon->save();
        return redirect()->route('admin.coupon')->with('status','Coupon has been updated Successfully !');
     }

     public function coupon_delete($id){
        $coupon=Coupon::find($id);
        $coupon->delete();
        return redirect()->route('admin.coupon')->with('status','Coupon has been Deleted Successfully !');
     }

     public function orders(){
        $orders=Order::orderBy('created_at','desc')->paginate(12);
        return view('admin.orders',[
            'orders'=>$orders,
        ]);
     }

     public function order_details($order_id){
        $order=Order::find($order_id);
        $orderItems=OrderItem::where('order_id',$order_id)->orderBy('id')->paginate(12);
        $transaction=Transaction::where('order_id',$order_id)->first();
        return view('admin.order_details',[
            'order'=>$order,
            'orderItems'=>$orderItems,
            'transaction'=>$transaction,
        ]);
     }

    public function update_order_status(Request $request){
        $order=Order::find($request->order_id);
        $order->status=$request->order_status;

        if($request->order_status == "delivered"){
            $order->delivered_date=Carbon::now();
        }elseif($request->order_status == "canceled"){
            $order->canceled_date=Carbon::now();
        }

        $order->save();

        if($request->order_status=="delivered"){
            $transaction=Transaction::where('order_id',$request->order_id)->first();
            $transaction->status="approved";
            $transaction->save();
        }
        return back()->with("status","Status Change Successfully !");
    } 

    public function slides(){
        $slides=Slide::orderBy('id','DESC')->paginate(12);
        return view('admin.slides',[
            'slides'=>$slides,
        ]);
    }

    public function slides_add(){
        return view('admin.slides-add');
    }

    public function slide_store(Request $request){
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'status'=>'required',
            'image'=>'required|mimes:png,jpeg,jpg|max:2028',
        ]);
        
        $slide=new Slide();
        $slide->tagline=$request->tagline;
        $slide->title=$request->title;
        $slide->subtitle=$request->subtitle;
        $slide->link=$request->link;
        $slide->status=$request->status;

        $image=$request->file('image');
        $file_extention=$request->file('image')->extension();
        $file_name=Carbon::now()->timestamp.'.'.$file_extention;
        $this->GenerateSlideThumnailsImage($image,$file_name);
        $slide->image=$file_name;
        $slide->save();

        return redirect()->route('admin.slides')->with('status','Slides Create Successfully !');
    }

    public function GenerateSlideThumnailsImage($image,$imageName){
        $destinationPath=public_path('uploads/slides');
        $img=Image::read($image->path());
        $img->cover(400,699,"top");
        $img->resize(400,699,function($constrant){
            $constrant->aspectRadio();
        })->save( $destinationPath.'/'.$imageName);
     }

    public function Slide_edit($id){
        $slide=Slide::find($id);
        return view('admin.slide-edit',[
           'slide'=>$slide,
        ]);
    }

    public function slide_update(Request $request){
        $request->validate([
            'tagline'=>'required',
            'title'=>'required',
            'subtitle'=>'required',
            'link'=>'required',
            'status'=>'required',
            'image'=>'required|mimes:png,jpeg,jpg|max:2028',
        ]);
        
        $slide=Slide::find($request->id);
        $slide->tagline=$request->tagline;
        $slide->title=$request->title;
        $slide->subtitle=$request->subtitle;
        $slide->link=$request->link;
        $slide->status=$request->status;

        
        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/slides').'/'.$slide->image)){
                File::delete(public_path('uploads/slides').'/'.$slide->image);
            }
            $image=$request->file('image');
            $file_extention=$request->file('image')->extension();
            $file_name=Carbon::now()->timestamp.'.'.$file_extention;
            $this->GenerateSlideThumnailsImage($image,$file_name);
            $slide->image=$file_name;
        }
        $slide->save();

        return redirect()->route('admin.slides')->with('status','Slides updates Successfully !');
    }
}
