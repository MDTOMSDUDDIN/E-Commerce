<?php

namespace App\Http\Controllers;

use App\Models\Brand;
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
        return redirect()->route('admin.brands')->with('status','Record has been updated successfully !');
    }
//delete Admin Brands 
    public function delete_brand($id){
        $brand=Brand::find($id);

        if(File::exists(public_path('uploads/brands').'/'.$brand->image)){
          File::delete(public_path('uploads/brands').'/'.$brand->image);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status','Record Brands has been Delete successfully !');
    }
}
