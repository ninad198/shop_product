<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
  return redirect('/admin/shops');
});

Route::get('/admin', function () {
  return redirect('/admin/shops');
});

Auth::routes();
  Route::group(['prefix' => 'admin', 'middleware' => ['auth'], 'as'=> 'admin.'], function () {
  Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
  Route::get('/shops', [App\Http\Controllers\ShopController::class, 'index'])->name('shop.list');
  Route::get('/shops/listdata', [App\Http\Controllers\ShopController::class, 'listData'])->name('shop.listdata');
  Route::get('/shop/add', [App\Http\Controllers\ShopController::class, 'create'])->name('shop.create');
  Route::post('/shop/store', [App\Http\Controllers\ShopController::class, 'store'])->name('shop.store');
  Route::get('/shop/edit/{id}', [App\Http\Controllers\ShopController::class, 'edit'])->name('shop.edit');
  Route::post('/shop/update', [App\Http\Controllers\ShopController::class, 'update'])->name('shop.update');
  Route::delete('/shop/delete/{id}', [App\Http\Controllers\ShopController::class, 'destroy'])->name('shop.delete');
  Route::get('/shop/view/{id}', [App\Http\Controllers\ShopController::class, 'show'])->name('shop.view');
  Route::get('/shops/import', [App\Http\Controllers\ShopController::class, 'import'])->name('shop.import');
  Route::post('/shops/import', [App\Http\Controllers\ShopController::class, 'import'])->name('shop.import');

  Route::get('/shop/{shop_id}/products/listdata', [App\Http\Controllers\ProductController::class, 'listData'])->name('product.listdata');
  Route::get('/shop/{shop_id}/product/add', [App\Http\Controllers\ProductController::class, 'create'])->name('product.create');
  Route::post('/shop/{shop_id}/product/store', [App\Http\Controllers\ProductController::class, 'store'])->name('product.store');
  Route::get('/shop/{shop_id}/product/edit/{id}', [App\Http\Controllers\ProductController::class, 'edit'])->name('product.edit');
  Route::post('/shop/{shop_id}/product/update', [App\Http\Controllers\ProductController::class, 'update'])->name('product.update');
  Route::delete('/shop/{shop_id}/product/delete/{id}', [App\Http\Controllers\ProductController::class, 'destroy'])->name('product.delete');
  Route::get('/shops/{shop_id}/product/import', [App\Http\Controllers\ProductController::class, 'import'])->name('product.import');
  Route::post('/shops/{shop_id}/product/import', [App\Http\Controllers\ProductController::class, 'import'])->name('product.import');

}); 
