<?php


use Illuminate\Support\Facades\Route;
use Bas\LaravelSdk\Http\Controllers\BasSuperAppController;

//Route::get('/bas', [BasSuperAppController::class, 'dashboard']);



Route::get('/bas/operation-failed', function () {
    return view('bas::operation_failed');
})->name('operation.failed');



Route::group(['middleware' => ['web']], function () {
//    Route::get('bas', function () {
//        // عرض الواجهة من مجلد views باستخدام الـ namespace الذي سجلته
//        return view('bas::dashboard');
//    })->name('bas');
    Route::get('/bas', [BasSuperAppController::class, 'dashboard']);
    Route::get('/bas/login', [BasSuperAppController::class, 'basLogin']);
    Route::post('/bas/get-user-info', [BasSuperAppController::class, 'getUserInfo']);
    Route::get('/bas/pay', [BasSuperAppController::class, 'initiatePayment']);
});
