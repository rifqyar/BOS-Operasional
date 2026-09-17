<?php

use App\Http\Controllers\Operation\BehandleController;
use App\Http\Controllers\Operation\PickupController;
use App\Http\Controllers\Operation\HoldController;
use App\Http\Controllers\Operation\MarshallingCicController;
use App\Http\Controllers\Operation\MarshallingYardController;
use App\Http\Controllers\Operation\RealisasiController;
use App\Http\Controllers\Operation\PlugReeferController;
use App\Http\Controllers\Operation\MonitoringReeferController;
use App\Http\Controllers\Operation\DeliveryController;
use App\Http\Controllers\Operation\InspectionOutController;
use App\Http\Controllers\Operation\OnChassisController;
use App\Http\Controllers\Operation\CopyYardController;
use App\Livewire\Dashboard;
use Illuminate\Support\Facades\Route;
use App\Livewire\Operation\MarshallingYard;
use App\Livewire\Operation\Index;
use App\Livewire\Operation\Pickup;
use App\Livewire\Operation\BehandleIn;
use App\Livewire\Operation\Hold;
// use App\Livewire\Operation\MarshallingCic;
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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('/', Dashboard::class)->name('home');

    /** Pickup */
    Route::get('pickup', Dashboard::class)->name('pickup');
    Route::post('pickup/search', [PickupController::class, 'search'])->name('pickup.search');
    Route::post('pickup/store', [PickupController::class, 'store'])->name('pickup.store');

    /** Behandle In */
    Route::get('/behandle-in', Dashboard::class)->name('behandlein');
    Route::post('behandle/search', [BehandleController::class, 'search'])->name('behandlein.search');
    Route::post('behandle/store', [BehandleController::class, 'store'])->name('behandlein.store');

    /** Hold */
    Route::get('/hold', Dashboard::class)->name('hold');

    Route::prefix('hold')
    ->name('hold.')
    ->group(function () {

        Route::post('/search', [
            HoldController::class,
            'search'
        ])->name('search');

        Route::get('/data', [
            HoldController::class,
            'indexData'
        ])->name('data');

        Route::post('/store', [
            HoldController::class,
            'store'
        ])->name('store');

        Route::post('/release', [
            HoldController::class,
            'release'
        ])->name('release');
    });


/** Marshalling CIC */
    Route::get('/marshalling-cic', Dashboard::class)->name('marshallingcic');
    Route::post('/marshalling-cic/search', [MarshallingCicController::class, 'search'])->name('marshallingcic.search');
    Route::post('/marshalling-cic/store', [MarshallingCicController::class, 'store'])->name('marshallingcic.store');
    Route::post('/marshalling-cic/detail', [MarshallingCicController::class, 'detail'])->name('marshallingcic.detail');
    Route::get('/marshalling-cic/data',[MarshallingCicController::class, 'indexData'])->name('marshallingcic.data');

    /** Marshalling Yard */
    Route::get('/marshalling-yard', Dashboard::class)->name('marshallingyard');
    Route::get('/marshalling-yard/data', [MarshallingYardController::class, 'indexData'])->name('marshallingyard.data');
    Route::post('/marshalling-yard/search', [MarshallingYardController::class, 'search'])->name('marshallingyard.search');
    Route::post('/marshalling-yard/detail', [MarshallingYardController::class, 'detail'])->name('marshallingyard.detail');
    Route::post('/marshalling-yard/store', [MarshallingYardController::class, 'store'])->name('marshallingyard.store');

            /** Realisasi / Pemeriksaan Behandle */
    Route::get('/realisasi', Dashboard::class)->name('realisasi');
    Route::post('/realisasi/search', [RealisasiController::class, 'search'])->name('realisasi.search');
    Route::post('/realisasi/detail', [RealisasiController::class, 'detail'])->name('realisasi.detail');
    Route::post('/realisasi/store', [RealisasiController::class, 'store'])->name('realisasi.store');
    
    Route::get('/plug-reefer', Dashboard::class)->name('plugreefer');
    Route::post('/plug-reefer/search', [PlugReeferController::class, 'search'])->name('plugreefer.search');

    Route::post('/plug-reefer/detail', [PlugReeferController::class, 'detail'])->name('plugreefer.detail');

    Route::post('/plug-reefer/store', [PlugReeferController::class, 'store'])->name('plugreefer.store');

    /** Monitoring Reefer */
    Route::get('/monitoring-reefer', Dashboard::class)->name('monitoringreefer');

    Route::post('/monitoring-reefer/search', [MonitoringReeferController::class, 'search'])->name('monitoringreefer.search');

    Route::post('/monitoring-reefer/detail', [MonitoringReeferController::class, 'detail'])->name('monitoringreefer.detail');

    Route::post('/monitoring-reefer/store', [MonitoringReeferController::class, 'store'])->name('monitoringreefer.store');

    /** Delivery */
    Route::get('/delivery', Dashboard::class)->name('delivery');
    Route::post('/delivery/search', [DeliveryController::class, 'search'])->name('delivery.search');
    Route::post('/delivery/detail', [DeliveryController::class, 'detail'])->name('delivery.detail');
    Route::post('/delivery/store', [DeliveryController::class, 'store'])->name('delivery.store');

    /** Inspection Out */
    Route::get('/inspection-out', Dashboard::class)->name('inspectionout');
    Route::post('/inspection-out/search', [InspectionOutController::class, 'search'])->name('inspectionout.search');
    Route::post('/inspection-out/store', [InspectionOutController::class, 'store'])->name('inspectionout.store');
    
    /** On Chassis */
    Route::get('/on-chassis', Dashboard::class)->name('onchassis');
    Route::post('/on-chassis/search', [OnChassisController::class, 'search'])->name('onchassis.search');
    Route::post('/on-chassis/store', [OnChassisController::class, 'store'])->name('onchassis.store');

    /** Copy Yard */
    Route::get('/copy-yard', Dashboard::class)->name('copyyard');
    Route::post('/copy-yard/search', [CopyYardController::class, 'search'])->name('copyyard.search');
    Route::post('/copy-yard/store', [CopyYardController::class, 'store'])->name('copyyard.store');


    // Route::redirect('settings', 'settings/profile');

    // Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    // Volt::route('settings/password', 'settings.password')->name('settings.password');
    // Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

// Route::livewire(
//     '/',
//     Index::class
// )->name('home');

// Route::livewire(
//     '/dashboard',
//     Index::class
// )->name('dashboard');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - PICKUP
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/pickup',
//     Pickup::class
// )->name('operation.pickup');

// /*
// |--------------------------------------------------------------------------
// | OPERATION - HOLD
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/hold',
//     Hold::class
// )->name('operation.hold');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - MARSHALLING YARD
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/marshalling-yard',
//     MarshallingYard::class
// )->name('operation.marshalling-yard');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - INSPECTION
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/inspection',
//     Inspection::class
// )->name('operation.inspection');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - PLUG REEFER
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/plug-reefer',
//     PlugReefer::class
// )->name('operation.plug-reefer');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - MONITORING REEFER
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/monitoring-reefer',
//     MonitoringReefer::class
// )->name('operation.monitoring-reefer');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - DELIVERY
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/delivery',
//     Delivery::class
// )->name('operation.delivery');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - INSPECTION OUT
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/inspection-out',
//     InspectionOut::class
// )->name('operation.inspection-out');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - ON CHASSIS
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/on-chassis',
//     OnChassis::class
// )->name('operation.on-chassis');


// /*
// |--------------------------------------------------------------------------
// | OPERATION - COPY YARD
// |--------------------------------------------------------------------------
// */

// Route::get(
//     '/operation/copy-yard',
//     function () {
//         return 'COPY YARD - Coming Soon';
//     }
// )->name('operation.copy-yard');

require __DIR__ . '/auth.php';
