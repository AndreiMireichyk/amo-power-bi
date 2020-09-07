<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/amocrm_webhook', function (Request $request) {
    \Illuminate\Support\Facades\Storage::disk('local')->put('amocrm.txt', var_export($request->all(), true));
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
