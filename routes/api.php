<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\BackendApi\ClientTypeController;
use App\Http\Controllers\BackendApi\ClinetController;
use App\Http\Controllers\BackendApi\ClinetLandDocumentController;
use App\Http\Controllers\BackendApi\NotificationController;
use App\Http\Controllers\BackendApi\RoleController;
use App\Http\Controllers\BackendApi\TransactionController;
use App\Http\Controllers\BackendApi\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/login', [AuthenticatedSessionController::class, 'login'])->name('login');

Route::middleware(['auth:admin-api', 'scopes:admin'])->group(function () {
    /**
     * !! Our routes to be protected will go in here
     */
    Route::get('/logout', [AuthenticatedSessionController::class, 'logout']);

    /**
     * route for client type
     */
    Route::prefix('client_type')->group(function () {
        Route::get('/index', [ClientTypeController::class, 'index']);
        Route::post('/store', [ClientTypeController::class, 'store']);
        Route::get('/show/{id}', [ClientTypeController::class, 'show']);
        Route::post('/update', [ClientTypeController::class, 'update']);
        Route::get('delete/{id}', [ClientTypeController::class, 'destroy']);
    });

    /**
     * route for client
     */
    Route::prefix('client')->group(function () {
        Route::get('/index', [ClinetController::class, 'index']);
        Route::get('/all', [ClinetController::class, 'all']);
        Route::post('/store', [ClinetController::class, 'store']);
        Route::get('/show/{id}', [ClinetController::class, 'show']);
        Route::post('/update', [ClinetController::class, 'update']);
        Route::get('delete/{id}', [ClinetController::class, 'destroy']);
    });

    /**
     * route for clients land document
     */
    Route::prefix('land')->group(function () {
        Route::get('/index', [ClinetLandDocumentController::class, 'index']);
        Route::get('/all', [ClinetLandDocumentController::class, 'all']);
        Route::post('/store', [ClinetLandDocumentController::class, 'store']);
        Route::get('/show/{id}', [ClinetLandDocumentController::class, 'show']);
        Route::post('/update', [ClinetLandDocumentController::class, 'update']);
        Route::get('delete/{id}', [ClinetLandDocumentController::class, 'destroy']);
    });

    Route::prefix('profile')->group(function () {
        Route::get('/', [\App\Http\Controllers\BackendApi\ProfileController::class, 'index']);
    });

    /**
     * route for transaction
     */
    Route::prefix('transaction')->group(function () {
        Route::get('index', [TransactionController::class, 'index']);
        Route::post('/store', [TransactionController::class, 'store']);
        Route::get('/show/{id}', [TransactionController::class, 'show']);
        Route::post('/update', [TransactionController::class, 'update']);
        Route::get('delete/{id}', [TransactionController::class, 'delete']);
    });

    Route::prefix('role')->group(function () {
        Route::get('/index', [RoleController::class, 'index']);
    });

    Route::prefix('user')->group(function () {
        Route::post('/store', [UserController::class, 'store']);
        Route::get('/index', [UserController::class, 'index']);
        Route::get('/show/{id}', [UserController::class, 'show']);
        Route::post('/update', [UserController::class, 'update']);
        Route::get('delete/{id}', [UserController::class, 'destroy']);
    });

    Route::prefix('notification')->group(function () {
        Route::get('/all', [NotificationController::class, 'all']);
        Route::get('/unread', [NotificationController::class, 'unreadNotification']);
        Route::get('/markasread/all', [NotificationController::class, 'markAsReadNotification']);
    });
});
