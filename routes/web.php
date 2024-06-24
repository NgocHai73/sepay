<?php

use Illuminate\Support\Facades\Route;



use App\Http\Controllers\SepayController;

Route::get('/payment', [SepayController::class, 'index'])->name('payment.index');
Route::post('/payment/process', [SepayController::class, 'processPayment'])->name('payment.process');
Route::get('/payment/success', [SepayController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment/failure', [SepayController::class, 'paymentFailure'])->name('payment.failure');
