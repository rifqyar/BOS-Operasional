<?php

use App\Livewire\Dashboard;
use App\Http\Controllers\PickupController;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('/', Dashboard::class)->name('home');
    Route::get('pickup', Dashboard::class)->name('pickup.index');

    Route::post('pickup/search', [PickupController::class, 'search'])->name('pickup.search');
    Route::post('pickup/store', [PickupController::class, 'store'])->name('pickup.store');

    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

require __DIR__ . '/auth.php';
