<?php

use App\Http\Controllers\CronController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Gateway\Paylabs\PaylabsPaymentController;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('rekening-info/{username}',[UserController::class,'rekeningInfo']);
Route::get('check-user/{username}',[UserController::class,'checkUser']);

Route::get('/v1/notify',[PaylabsPaymentController::class,'notify']);

Route::get('/convert-balance', function () {
    $conversionDetails = (new CronController)->convertBBalanceToBalance();
    return response()->json($conversionDetails);
});
Route::get('/convert-point', function () {
    $conversionDetails = (new CronController)->convertPointToBalance();
    return response()->json($conversionDetails);
});

