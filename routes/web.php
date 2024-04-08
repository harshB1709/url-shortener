<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UrlController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/url/{url:short_code}', [UrlController::class, 'accessUrl'])->name('url.access');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    //URLs
    Route::prefix('urls')->name('url.')->group(function () {
        Route::get('/', [UrlController::class, 'index'])->name('index');
        Route::post('/store', [UrlController::class, 'store'])->name('store');
        Route::post('/{url}/update', [UrlController::class, 'update'])->name('update');
        Route::post('/{url}/deactivate', [UrlController::class, 'deactivate'])->name('deactivate');
        Route::delete('/{url}', [UrlController::class, 'delete'])->name('delete');
    });
});

require __DIR__.'/auth.php';
