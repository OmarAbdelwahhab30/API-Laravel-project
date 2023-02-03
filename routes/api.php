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

// ************************************************** SANCTUM ************************************************************//
//Route::post("login",[\App\Http\Controllers\API\SanctumController::class,"login"]);
//Route::post("register",[\App\Http\Controllers\API\SanctumController::class,"register"]);
//Route::post("logout",[\App\Http\Controllers\API\SanctumController::class,"logout"])->middleware("auth:sanctum");


Route::group(['prefix' => 'v1', 'middleware' => ['ApiGate']], function(){
    Route::post("checked",function (){
        return response()->json("حمدا لله على سلامتكم");
    });
});


Route::group(['prefix' => 'v1','namespace' => 'API'], function(){
    Route::post("register","AuthController@register");
    Route::post("login",[ 'as' => 'login', 'uses' =>"AuthController@login"]);

    Route::group(['middleware' => ['jwt.verify']], function(){

        Route::post("logout","AuthController@logout");
        Route::post("refresh","AuthController@refresh");
        Route::post("getUser","Authcontroller@GetUser");

    });

});



Route::group(['prefix' => 'v1','middleware' => ['jwt.verify','ApiGate','CheckRole:admin'], 'namespace' => 'API'],function() {
    Route::group(['middleware' => 'LangSwitcher'],function (){
        Route::post("GetCategs","CategoryController@index");
        Route::post("showCategByID","CategoryController@show");
    });
    Route::post("AddCateg","CategoryController@store");
    Route::post("UpdateCateg","CategoryController@update");
    Route::post("DeleteCateg","CategoryController@destroy");
 });


//get user roles
// jwt.verify is necessary to parse token for usage
Route::group(['prefix' => 'v1', 'middleware' => ['jwt.verify','ApiGate'],'namespace' => 'API'], function() {
    Route::post("userRoles", "AuthController@GetRoles");
});



Route::group(['prefix' => 'v1' ,'middleware' => ['ApiGate','jwt.verify']],function (){
    Route::post("CheckToken",function (){
        return "Token is Valid";
    });
});

Route::post("uploadimage","ImageController@uploadimage");
