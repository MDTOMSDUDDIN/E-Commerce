<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WishlistController;
use App\Http\Middleware\AuthAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::get('/',[HomeController::class, 'index'])->name('home.index');
route::get('/shop',[ShopController::class,'index'])->name('shop.index');
route::get('/shop/{product_slug}',[ShopController::class,'product_details'])->name('shop.product.details');

//cartController ------------
route::get('/cart',[CartController::class,"index"])->name('cart.index');
route::post('/cart/add',[CartController::class,"add_to_cart"])->name('cart.add');
route::put('/cart/increase-quantity/{rowId}',[CartController::class,"increase_cart_quantity"])->name('cart.qty.increase');
route::put('/cart/decrease-quantity/{rowId}',[CartController::class,"decrease_cart_quantity"])->name('cart.qty.decrease');
route::delete('/cart/remove/{rowId}',[CartController::class,"remove_item"])->name('cart.item.remove');
route::delete('/cart/clear',[CartController::class,"empty_cart"])->name('cart.destroy');
// ---------------------------------------------------------------------
route::post('/wishlist/add',[WishlistController::class,"add_to_wishlist"])->name('wishlist.add');

//group auth,AuthAdmin middleware   ------------------------------------------------------------------------
Route::middleware(['auth'])->group( function(){
    route::get('/account_dashboard',[UserController::class,'index'])->name('user.index');
});
Route::middleware(['auth',AuthAdmin::class])->group( function(){
    route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    
//Brands section -------------------------------------------------------------------------------------------
    route::get('/brands',[AdminController::class,'brands'])->name('admin.brands');
    route::get('/admin/brand/add',[AdminController::class,'add_brand'])->name('admin.brand.add');
    route::post('/admin/brand/store',[AdminController::class,'brand_store'])->name('admin.brand.store');
    route::get('/admin/brand/edit/{id}',[AdminController::class,'edit_brand'])->name('admin.brand.edit');
    route::put('/admin/brand/update',[AdminController::class,'update_brand'])->name('admin.brand.update');
    route::delete('/admin/brand/{id}/delete',[AdminController::class,'delete_brand'])->name('admin.brand.delete');
    
//categories section ----------------------------------------------------------------------------------------
    route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');
    route::get('/admin/category/add',[AdminController::class,'category_add'])->name('admin.category.add');
    route::post('/admin/category/store',[AdminController::class,'category_store'])->name('admin.category.store');
    route::get('/admin/category/edit/{id}',[AdminController::class,'category_edit'])->name('admin.category.edit');
    route::put('/admin/category/update',[AdminController::class,'update_category'])->name('admin.category.update');
    route::delete('/admin/category/delete/{id}',[AdminController::class,'delete_category'])->name('admin.category.delete');
// //Products section --------------------------------------------------------------------------------------------------
    route::get('admin/products',[ProductController::class,'products'])->name('admin.products');
    route::get('admin/product/add',[ProductController::class,'product_add'])->name('admin.product.add');
    route::post('admin/product/store',[ProductController::class,'product_store'])->name('admin.product.store');
    route::get('/admin/product/edit/{id}',[ProductController::class,'product_edit'])->name('admin.product.edit');
    route::put('/admin/product/update',[ProductController::class,'product_update'])->name('admin.product.update');
    route::delete('/admin/product/delete/{id}',[ProductController::class,'delete_product'])->name('admin.product.delete');
});