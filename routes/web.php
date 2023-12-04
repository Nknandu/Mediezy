<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocterController;
use App\Http\Controllers\FullCalenderController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\specializeController;
use App\Http\Controllers\SpecificationController;
use App\Http\Controllers\SubspecificationController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    if (Auth::check()) {
        Auth::logout();
    }
    return view('welcome');
});

Route::get('/login',function (){
    return view('login');
});


    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/Specialization', [SpecificationController::class, 'index'])->name('Specialization.index');
    Route::get('/Subspecialization', [SubspecificationController::class, 'index'])->name('Subspecialization.index');
    Route::get('/Docter', [DocterController::class, 'index'])->name('Docter.index');
    Route::get('/Docter/create', [DocterController::class, 'create'])->name('Docter.create');
    Route::get('/specialize', [specializeController::class, 'index'])->name('specialize.index');
    Route::get('/schedulemanager', [ScheduleController::class, 'index'])->name('schedulemanager.index');
    Route::get('/Tokengeneration', [ScheduleController::class, 'create'])->name('Tokengeneration.create');
    Route::get('/banner', [BannerController::class, 'index'])->name('bannerImage.index');
    Route::get('/Docteredit/{userId}', [DocterController::class, 'edit'])->name('Docter.edit');
    Route::controller(FullCalenderController::class)->group(function(){
        Route::get('fullcalender', 'index');
        Route::post('fullcalenderAjax', 'ajax');
    });

    Route::get('/medicine',[MedicineController::class, 'index'])->name('medicine.index');


    Route::get('/showToken',function () {
        return view('ShowTokens');
});




require __DIR__.'/auth.php';
