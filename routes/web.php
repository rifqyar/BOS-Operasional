<?php

use App\Livewire\Dashboard;
use App\Http\Controllers\PickupController;
use Illuminate\Support\Facades\Route;
use App\Livewire\Operation\MarshallingYard;
use App\Livewire\Operation\Index;
use App\Livewire\Operation\Pickup;
use App\Livewire\Operation\BehandleIn;
use App\Livewire\Operation\Hold;
use App\Livewire\Operation\MarshallingCic;
use App\Livewire\Operation\Inspection;
use App\Livewire\Operation\PlugReefer;
use App\Livewire\Operation\MonitoringReefer;
use App\Livewire\Operation\Delivery;
use App\Livewire\Operation\InspectionOut;
use App\Livewire\Operation\OnChassis;


/*
|--------------------------------------------------------------------------
| HOME
|--------------------------------------------------------------------------
*/

// Route::middleware(['auth', 'verified'])->group(function () {
//     Route::get('dashboard', Dashboard::class)->name('dashboard');
//     Route::get('/', Dashboard::class)->name('home');
//     Route::get('pickup', Dashboard::class)->name('pickup.index');

//     Route::post('pickup/search', [PickupController::class, 'search'])->name('pickup.search');
//     Route::post('pickup/store', [PickupController::class, 'store'])->name('pickup.store');

//     Route::redirect('settings', 'settings/profile');

//     Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
//     Volt::route('settings/password', 'settings.password')->name('settings.password');
//     Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
// });

Route::livewire(
    '/',
    Index::class
)->name('home');

Route::livewire(
    '/dashboard',
    Index::class
)->name('dashboard');


/*
|--------------------------------------------------------------------------
| OPERATION - PICKUP
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/pickup',
    Pickup::class
)->name('operation.pickup');


/*
|--------------------------------------------------------------------------
| OPERATION - BEHANDLE IN
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/behandle-in',
    BehandleIn::class
)->name('operation.behandle-in');


/*
|--------------------------------------------------------------------------
| OPERATION - HOLD
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/hold',
    Hold::class
)->name('operation.hold');


/*
|--------------------------------------------------------------------------
| OPERATION - MARSHALLING CIC
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/marshallingcic',
    MarshallingCic::class
)->name('operation.marshallingcic');


/*
|--------------------------------------------------------------------------
| OPERATION - MARSHALLING YARD
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/marshalling-yard',
    MarshallingYard::class
)->name('operation.marshalling-yard');


/*
|--------------------------------------------------------------------------
| OPERATION - INSPECTION
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/inspection',
    Inspection::class
)->name('operation.inspection');


/*
|--------------------------------------------------------------------------
| OPERATION - PLUG REEFER
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/plug-reefer',
    PlugReefer::class
)->name('operation.plug-reefer');


/*
|--------------------------------------------------------------------------
| OPERATION - MONITORING REEFER
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/monitoring-reefer',
    MonitoringReefer::class
)->name('operation.monitoring-reefer');


/*
|--------------------------------------------------------------------------
| OPERATION - DELIVERY
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/delivery',
    Delivery::class
)->name('operation.delivery');


/*
|--------------------------------------------------------------------------
| OPERATION - INSPECTION OUT
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/inspection-out',
    InspectionOut::class
)->name('operation.inspection-out');


/*
|--------------------------------------------------------------------------
| OPERATION - ON CHASSIS
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/on-chassis',
    OnChassis::class
)->name('operation.on-chassis');


/*
|--------------------------------------------------------------------------
| OPERATION - COPY YARD
|--------------------------------------------------------------------------
*/

Route::get(
    '/operation/copy-yard',
    function () {
        return 'COPY YARD - Coming Soon';
    }
)->name('operation.copy-yard');