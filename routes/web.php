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

route::post('/cart/apply/coupon',[CartController::class,"apply_coupon_code"])->name('cart.apply.coupon');
route::delete('/cart/coupon/remove',[CartController::class,"remove_coupon_code"])->name('cart.coupon.remove');

route::get('/checkout',[CartController::class,'checkout'])->name('cart.checkout');


//WishlistController ---------------------------------------------------------------------
route::post('/wishlist/add',[WishlistController::class,"add_to_wishlist"])->name('wishlist.add');
route::get('/wishlist',[WishlistController::class,"index"])->name('wishlist.index');
route::delete('/wishlist/item/remove/{rowId}',[WishlistController::class,"remove_item"])->name('wishlist.item.remove');
route::delete('/wishlist/clear',[WishlistController::class,"empty_wishlist"])->name('wishlist.items.clear');
Route::post('wishlist/move/cart/{rowId}',[WishlistController::class,"remove_to_cart"])->name('wishlist.move.to.cart');

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
// -------------------------------------------------------------------------------------
    route::get('/admin/coupon',[AdminController::class,'coupon'])->name('admin.coupon');
    route::get('/admin/coupon/add',[AdminController::class,'coupon_add'])->name('admin.coupon.add');
    route::post('/admin/coupon/store',[AdminController::class,'coupon_store'])->name('admin.coupon.store');
    route::get('/admin/coupon/edit/{id}',[AdminController::class,'coupon_edit'])->name('admin.coupon.edit');
    route::put('admin/coupon/update',[AdminController::class,'coupon_update'])->name('admin.coupon.update');
    route::delete('admin/coupon/delete/{id}',[AdminController::class,'coupon_delete'])->name('admin.coupon.delete');

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