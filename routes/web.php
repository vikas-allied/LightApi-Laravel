<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/custom-facade', function () {
    return response(\App\Greeting\GreetingFacade::greet(), 200);
})->name('custom-facade');
