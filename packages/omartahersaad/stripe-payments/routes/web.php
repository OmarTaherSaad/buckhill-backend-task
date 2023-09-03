<?php

use Illuminate\Support\Facades\Route;
use OmarTaherSaad\StripePayments\Http\Controllers\StripePaymentController;

//Route for performing a payment
Route::get('pay', [StripePaymentController::class, 'pay'])->name('pay');
//Route for handling the payment callback
Route::get('callback', [StripePaymentController::class, 'callback'])->name('callback');
