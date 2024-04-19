<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\ChatController;


Route::group(['prefix' => 'v1'], function(){

    Route::group(['prefix' => 'auth'], function () {


        Route::controller(ForgotPasswordController::class)->group(function () {
           Route::get('/password/reset/{token}', 'showResetForm')->name('password.reset');
           Route::post('/password/email', 'sendResetLinkEmail');
           Route::post('/reset', 'reset')->name('password.update');
        });

        Route::controller(AuthController::class)->group(function () {
           Route::post('/login', 'login');
           Route::post('/signup', 'register');
           Route::post('/refresh', 'refresh');
           Route::get('/logout', 'logout');
        });



    });

    Route::controller(ChatController::class)->group(function () {
           Route::get('/chats', 'index');
           Route::post('/chats', 'store');
           Route::get('/chats/{chat}', 'show');
    });



    // Get a specific chat by its ID



    // Route::group(['middleware' => ['auth:api', 'admin']], function() {
    //    Route::get('/users', [AuthController::class, "getUsers"]);
    // });


});


