<?php

use App\Http\Controllers\v1\Admin\AppointmentController;
use App\Http\Controllers\v1\Admin\CustomerController;
use App\Http\Controllers\v1\Admin\DashboardController;
use App\Http\Controllers\v1\Admin\ServiceController;
use App\Http\Controllers\v1\Admin\SettingsController;
use App\Http\Controllers\v1\Auth\ForgetPasswordController;
use App\Http\Controllers\v1\Auth\LoginController;
use App\Http\Controllers\v1\Auth\RegisterController;
use App\Http\Controllers\v1\GeneralController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(["prefix" => "v1"], function () {

    // Clear Cache
    Route::get('/clear-cache', function () {
        Artisan::call('optimize:clear');
        return "Cache Cleared Successfully";
    });

    // All General open routes
    Route::group(['prefix' => 'general'], function () {
        // Customer open routes
        Route::group(['prefix' => 'customer'], function () {
            Route::post('/create', [GeneralController::class, 'createCustomer']);
            Route::post('/appointment/create', [GeneralController::class, 'createAppointment']);
            Route::get('/services/all', [GeneralController::class, 'allServices']);
        });
    });

    // Authentication Route
    Route::group(["prefix" => "auth"], function () {
        Route::post('account/register', [RegisterController::class, 'register']);
        Route::post('account/login', [LoginController::class, 'login']);
        Route::post('account/forgot/password', [ForgetPasswordController::class, 'requestResetPasswordLink']);
        Route::get('account/verify/forgot/password/{token}', [ForgetPasswordController::class, 'verifyResetPassword']);
        Route::post('account/forgot/update/password', [ForgetPasswordController::class, 'adminResetPassword']);
        Route::get('account/logout', [LoginController::class, 'logout']);
    });

    //Admin Controller
    Route::group(['prefix' => 'admin', 'namespace' => 'v1\Admin', 'middleware' => ["auth:api", "admin"]], function () {

        // Dashboard controller
        Route::group(['prefix' => 'dashboard'], function () {
            Route::get('/', [DashboardController::class, 'index']);
        });

        // Appointment controller
        Route::group(['prefix' => 'appointment'], function () {
            Route::post('/', [AppointmentController::class, 'index']);
            Route::get('/{id}', [AppointmentController::class, 'show']);
            Route::put('/update/{id}', [AppointmentController::class, 'update']);
        });

        // Service controller
        Route::group(['prefix' => 'services'], function () {
            Route::get('/', [ServiceController::class, 'index']);
            Route::post('/create', [ServiceController::class, 'create']);
            Route::get('/{id}', [ServiceController::class, 'show']);
            Route::put('/update/{id}', [ServiceController::class, 'update']);
            Route::delete('/delete/{id}', [ServiceController::class, 'delete']);
        });

        // Customer controller
        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', [CustomerController::class, 'index']);
            Route::get('/{id}', [CustomerController::class, 'show']);
        });

        // Settings controller
        Route::group(['prefix' => 'settings'], function () {
            Route::get('/{id}', [SettingsController::class, 'show']);
            Route::put('/update/profile/{id}', [SettingsController::class, 'updateProfile']);
            Route::post('/update/password', [SettingsController::class, 'updatePassword']);
        });
    });

    //Customer Controller
    Route::group(['prefix' => 'customer', 'namespace' => 'v1\Customer', 'middleware' => ["auth:api", "customer"]], function () {});
});
