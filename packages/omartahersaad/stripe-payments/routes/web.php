<?php

use Illuminate\Support\Facades\Route;
use OmarTaherSaad\StripePayments\Http\Controllers\StripePaymentController;

//Route for performing a payment
Route::get('pay', [StripePaymentController::class, 'pay'])->name('pay');
