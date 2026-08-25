<?php

use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/customer', function () {
        return 'Customer Area';
    });
});
