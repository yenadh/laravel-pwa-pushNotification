<?php

use App\Http\Controllers\NotificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::post('save-sub', [NotificationController::class, 'saveSubscription']);
Route::post('send-notification', [NotificationController::class, 'sendNotification']);
