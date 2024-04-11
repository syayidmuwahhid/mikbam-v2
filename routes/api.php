<?php

use App\Http\Controllers\API\DHCPServer;
use App\Http\Controllers\API\DNS;
use App\Http\Controllers\API\Interfaces;
use App\Http\Controllers\API\InternetGateway;
use App\Http\Controllers\API\IPAddress;
use App\Http\Controllers\API\Lainnya;
use App\Http\Controllers\API\Login;
use App\Http\Controllers\API\SimpleQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(Login::class)->group(function(){
    Route::post('/login', 'login');
});

Route::controller(IPAddress::class)->prefix('/ip-address')->group(function(){
    Route::post('/', 'index');
    Route::post('/store', 'store');
    Route::post('/stat-update', 'onoff');
    Route::delete('/', 'destroy');
});

Route::controller(DHCPServer::class)->prefix('/dhcp-server')->group(function(){
    Route::post('/', 'index');
    Route::post('/store', 'store');
    Route::post('/stat-update', 'onoff');
    Route::delete('/', 'destroy');
    Route::post('/generate-pool', 'generatePool');
});

Route::controller(Interfaces::class)->prefix('/interfaces')->group(function(){
    Route::post('/', 'index');
    Route::post('/get-ip', 'getIP');
    // Route::post('/interfaces/store', 'store');
    // Route::post('/interfaces/stat-update', 'onoff');
    // Route::delete('/interfaces', 'destroy');
});

Route::controller(DNS::class)->prefix('/dns')->group(function(){
    Route::post('/', 'index');
});

Route::controller(InternetGateway::class)->prefix('/internet-gateway')->group(function(){
    Route::post('/', 'index');
    Route::post('/run-auto', 'auto');
    Route::post('/run', 'manual');
});

Route::controller(SimpleQueue::class)->prefix('/queue-simple')->group(function(){
    Route::post('/', 'index');
    // Route::post('/queue-simple/store', 'store');
    // Route::post('/queue-simple/stat-update', 'onoff');
    // Route::delete('/queue-simple', 'destroy');
});

Route::controller(Lainnya::class)->group(function(){
    Route::post('/get-clock', 'getClock');
    Route::post('/get-resource', 'getResource');
    Route::post('/get-log', 'getLog');
    Route::post('/get-traffic', 'getTraffic');
});