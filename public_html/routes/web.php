<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FormController;


Route::get('/', function () {
    return view('home');
});

Route::get('/features', function () {
    return view('features');
});

Route::get('/pricing', function () {
    return view('pricing');
});

Route::get('/faq', function () {
    return view('faq');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::post('/contact', [FormController::class, 'contact']);

Route::get('/about', function () {
    return view('about');
});

Route::get('/terms-and-conditions', function () {
    return view('terms-and-conditions');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});

Route::get('/gdpr', function () {
    return view('gdpr');
});

Route::get('/cookie-policy', function () {
    return view('cookie-policy');
});

Route::get('/refund-policy', function () {
    return view('refund-policy');
});




