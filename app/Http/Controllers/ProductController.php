<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\category;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;


class ProductController extends Controller
{
    public function products(){
         $products=Product::orderBy('created_at','desc')->paginate(10);
         return  view('admin.products',[
            'products'=>$products,
         ]);
    }

    public function product_add(){
        $categories=category::select('id','name')->orderby('name')->get();
        $brands=Brand::select('id','name')->orderby('name')->get();
        return view('admin.product_add',[
            'categories'=>$categories,
            'brands'=>$brands,
        ]);
    }
     
    public function product_store(Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:products,slug',
            'short_description'=>'required',
            'description'=>'required',
            'regular_price'=>'required',
            'sale_price'=>'required',
            'SKU'=>'required',
            'stock_status'=>'required',
            'featured'=>'required',
            'quantity'=>'required',
            'image'=>'required|mimes:png,jpg,jpeg,|max:2024',
            'category_id'=>'required',
            'brand_id'=>'required',
        ]);

        $product=new Product();
        $product->name=$request->name;
        $product->slug=Str::slug($request->name);
        $product->short_description=$request->short_description;
        $product->description=$request->description;
        $product->regular_price=$request->regular_price;
        $product->sale_price=$request->sale_price;
        $product->SKU=$request->SKU;
        $product->stock_status=$request->stock_status;
        $product->featured=$request->featured;
        $product->quantity=$request->quantity;
        $product->category_id=$request->category_id;
        $product->brand_id=$request->brand_id;

       
        $current_timestamp=Carbon::now()->timestamp;

        if($request->hasFile('image')){
          $image=$request->file('image');
          $imageName=$current_timestamp . '.' . $image->extension();
          $this->GenerateProductThumnailsImage($image,$imageName);
          $product->image=$imageName;
        }

        $gallery_arr=array();
        $gallery_images= "";
        $counter= 1; 

        if($request->hasFile('images')){
            $allowedFileExtension=['jpg','jpeg','png'];
            $files=$request->file('images');

            foreach ($files as $file) {
                $gextension=$file->getClientOriginalExtension();

                $gcheck=in_array($gextension,$allowedFileExtension);
                if($gcheck){
                    $gfileName=$current_timestamp . "-" . $counter . "." . $gextension;
                    $this->GenerateProductThumnailsImage($file,$gfileName);
                    array_push($gallery_arr , $gfileName);
                    $counter= $counter + 1;
                }
            }
            $gallery_images=implode(',',$gallery_arr);
        }
        $product->images=$gallery_images;
        $product->save();
        return redirect(route('admin.products'))->with('status','product has been Add Successfully ');

    }

    public function GenerateProductThumnailsImage($image,$imageName){
        $destinationPathThumbnails=public_path('uploads/products/thumbnails');
        $destinationPath=public_path('uploads/products');
        $img=Image::read($image->path());

        $img->cover(540,689,"top");
        $img->resize(540,689,function($constrant){
            $constrant->aspectRadio();
        })->save( $destinationPath.'/'.$imageName);

        $img->resize(104,104,function($constrant){
            $constrant->aspectRadio();
        })->save( $destinationPathThumbnails.'/'.$imageName);
     }

//update 
     public function product_edit($id){
        $product=Product::find($id);
        $categories=category::select('id','name')->orderby('name')->get();
        $brands=Brand::select('id','name')->orderby('name')->get();

        return view('admin.product-edit',[
            'product'=>$product,
            'categories'=>$categories,
            'brands'=>$brands,
        ]);
     }

     public function product_update(Request $request){
        $request->validate([
            'name'=>'required',
            'slug'=>'required|unique:products,slug'.$request->id,
            'short_description'=>'required',
            'description'=>'required',
            'regular_price'=>'required',
            'sale_price'=>'required',
            'SKU'=>'required',
            'stock_status'=>'required',
            'featured'=>'required',
            'quantity'=>'required',
            'image'=>'mimes:png,jpg,jpeg,|max:2024',
            'category_id'=>'required',
            'brand_id'=>'required',
        ]);
        $product=Product::find($request->id);
        $product->name=$request->name;
        $product->slug=Str::slug($request->name);
        $product->short_description=$request->short_description;
        $product->description=$request->description;
        $product->regular_price=$request->regular_price;
        $product->sale_price=$request->sale_price;
        $product->SKU=$request->SKU;
        $product->stock_status=$request->stock_status;
        $product->featured=$request->featured;
        $product->quantity=$request->quantity;
        $product->category_id=$request->category_id;
        $product->brand_id=$request->brand_id;

       
        $current_timestamp=Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products').'/'.$product->image)){
                File::delete(public_path('uploads/products').'/'.$product->image);
            }
            if(File::exists(public_path('uploads/products/thumbnails').'/'.$product->image)){
                File::delete(public_path('uploads/products/thumbnails').'/'.$product->image);
            }
            $image=$request->file('image');
            $imageName=$current_timestamp . '.' . $image->extension();
            $this->GenerateProductThumnailsImage($image,$imageName);
            $product->image=$imageName;
          }
  
          $gallery_arr=array();
          $gallery_images= "";
          $counter= 1; 
  
          if($request->hasFile('images')){
            foreach(explode(',',$product->images) as $ofile){
                if(File::exists(public_path('uploads/products').'/'.$ofile)){
                    File::delete(public_path('uploads/products').'/'.$ofile);
                }
                if(File::exists(public_path('uploads/products/thumbnails').'/'.$ofile)){
                    File::delete(public_path('uploads/products/thumbnails').'/'.$ofile);
                }
            }
              $allowedFileExtension=['jpg','jpeg','png'];
              $files=$request->file('images');
  
              foreach ($files as $file) {
                  $gextension=$file->getClientOriginalExtension();
  
                  $gcheck=in_array($gextension,$allowedFileExtension);
                  if($gcheck){
                      $gfileName=$current_timestamp . "-" . $counter . "." . $gextension;
                      $this->GenerateProductThumnailsImage($file,$gfileName);
                      array_push($gallery_arr , $gfileName);
                      $counter= $counter + 1;
                  }
              }
              $gallery_images=implode(',',$gallery_arr);
              $product->images=$gallery_images;
          }
          $product->save();
          return redirect(route('admin.products'))->with('status','update has been Add Successfully ');
  
     }
}
