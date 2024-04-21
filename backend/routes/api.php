<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\MessageController;


Route::group(['prefix' => 'v1'], function(){

    Route::group(['prefix' => 'auth'], function () {

        Route::controller(AuthController::class)->group(function () {
           Route::post('/login', 'login');
           Route::post('/signup', 'register');
           Route::post('/refresh', 'refresh');
           Route::get('/logout', 'logout');
        });

    });

    Route::controller(ChatController::class)->group(function () {
           Route::get('/chats', 'getChats');
           Route::get('/chats/last', 'getChatsLastMessage');
           Route::post('/chats', 'store');
           Route::get('/chats/{chat}', 'show');
    });

     Route::controller(MessageController::class)->group(function () {
           Route::get('/messages', 'getMessages');
           Route::get('/message/{chat_id}', 'getMessagesChat');
           Route::post('/messages', 'sendMessage');
           Route::get('/messages/{message}', 'showMessage');
           Route::post('/messages/reply', 'replyToMessage');
    });


});


