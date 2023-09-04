<?php

use Illuminate\Support\Facades\Route;
use OmarTaherSaad\CurrenTune\Http\Controllers\CurrencyConverterController;

Route::get('/', [CurrencyConverterController::class, 'convert'])->name('convert');
