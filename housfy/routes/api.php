<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\WebserviceController;
use App\Exceptions\ApiResourceNotFoundException;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('{resource}', [WebserviceController::class, 'index']);
Route::get('{resource}/{id}', [WebserviceController::class, 'show']);
Route::post('{resource}', [WebserviceController::class, 'store']);
Route::put('{resource}/{id}', [WebserviceController::class, 'update']);
Route::delete('{resource}/{id}', [WebserviceController::class, 'destroy']);

Route::any('{any}', function(){
	throw new ApiResourceNotFoundException();
})->where('any', '.*');
