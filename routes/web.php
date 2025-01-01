<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth'])->group( function(){
    route::get('/account_dashboard',[UserController::class,'index'])->name('user.index');
});
Route::middleware(['auth',AuthAdmin::class])->group( function(){
    route::get('/admin',[AdminController::class,'index'])->name('admin.index');
    route::get('/brands',[AdminController::class,'brands'])->name('admin.brands');
    route::get('/admin/brand/add',[AdminController::class,'add_brand'])->name('admin.brand.add');
    route::post('/admin/brand/store',[AdminController::class,'brand_store'])->name('admin.brand.store');
    route::get('/admin/brand/edit/{id}',[AdminController::class,'edit_brand'])->name('admin.brand.edit');
    route::put('/admin/brand/update',[AdminController::class,'update_brand'])->name('admin.brand.update');
    route::delete('/admin/brand/{id}/delete',[AdminController::class,'delete_brand'])->name('admin.brand.delete');
    
//categories section 
    route::get('/admin/categories',[AdminController::class,'categories'])->name('admin.categories');

});