<?php

use App\Http\Controllers\FrontController;
use Illuminate\Support\Facades\Route;


Route::get('/', [FrontController::class, 'index'])->name('front.index');
Route::get('/transaction', [FrontController::class, 'transaction'])->name('front.transaction');
Route::post('/transaction/details', [FrontController::class, 'transaction_details'])->name('front.transaction.details');
Route::get('/search', [FrontController::class, 'search'])->name('front.search');
Route::get('/store/details/{carStore:slug}', [FrontController::class, 'details'])->name('front.details');
Route::get('/booking/{carStore:slug}', [FrontController::class, 'booking'])->name('front.booking');
Route::post('/booking/{carStore:slug}/{carService:slug}', [FrontController::class, 'booking_store'])->name('front.booking.store');
Route::get('/booking/{carStore:slug}/{carService:slug}/payment', [FrontController::class, 'booking_payment'])
    ->name('front.booking.payment');

Route::post('/booking/payment/submit', [FrontController::class, 'booking_payment_store'])->name('front.booking.payment.store');
ROute::get('/booking/success/{bookingTransaction}', [FrontController::class, 'success_booking'])->name('front.success.booking');
