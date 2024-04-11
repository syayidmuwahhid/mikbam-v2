<?php

use App\Helpers\AnyHelper;
use App\Helpers\AnyHelpers;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/login', fn() => view('auth.login'))-> name('login');
Route::get('/logout', fn() => view('auth.logout'))-> name('logout');
Route::get('/', fn() => view('dashboard'));
Route::get('/ip-address', fn() => view('ip-address'));
Route::get('/internet-gateway', fn() => view('internet-gateway'));
Route::get('/dhcp-server', fn() => view('dhcp-server'));
Route::get('/setting', fn() => view('setting'));

Route::prefix('/demo')->group(function () {
  Route::get('/', fn() => view('dashboard'));
  Route::get('/ip-address', fn() => view('ip-address'));
  Route::get('/internet-gateway', fn() => view('internet-gateway'));
  Route::get('/dhcp-server', fn() => view('dhcp-server'));
});
