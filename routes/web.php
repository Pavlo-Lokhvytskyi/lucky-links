<?php

declare(strict_types=1);

use App\Http\Controllers\PlayController;
use App\Http\Controllers\RegistrationController;
use App\Http\Middleware\EnsureLinkIsActive;
use Illuminate\Support\Facades\Route;

Route::get('/', [RegistrationController::class, 'create'])->name('register');
Route::post('/register', [RegistrationController::class, 'store'])->name('register.store');

Route::middleware(EnsureLinkIsActive::class)
    ->prefix('play/{link:token}')
    ->name('play.')
    ->group(function (): void {
        Route::get('/', [PlayController::class, 'show'])->name('show');
        Route::get('/history', [PlayController::class, 'history'])->name('history');
        Route::post('/lucky', [PlayController::class, 'lucky'])->name('lucky');
        Route::post('/regenerate', [PlayController::class, 'regenerate'])->name('regenerate');
        Route::post('/deactivate', [PlayController::class, 'deactivate'])->name('deactivate');
    });
