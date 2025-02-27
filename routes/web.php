<?php

use App\Http\Controllers\PriceViewController;
use Illuminate\Support\Facades\Route;
use App\Jobs\PriceFetcherJob;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', [PriceViewController::class, 'index'])->name('prices.index');
