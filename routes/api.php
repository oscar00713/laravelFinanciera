<?php

use App\Models\User;
use App\Models\Controlpago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AbonoController;
use App\Http\Controllers\Api\UsersController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ControlpagoController;
use App\Http\Controllers\tools\UsersDataController;
use App\Http\Controllers\Analisis\AnalisisController;
use App\Http\Controllers\Api\CantidadBilleteController;
use App\Http\Controllers\Api\RecuperacionDiumController;
use App\Http\Controllers\historial\HistorialController;

//Route::get('/user', function (Request $request) {

// })->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     //     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::apiResources([
        '/users' => UsersController::class,
        '/control' => ControlpagoController::class,
        '/abonos' => AbonoController::class,
        '/recuperacionDias' => RecuperacionDiumController::class,
        '/cantidad' => CantidadBilleteController::class,
    ]);

    Route::get('/dataUsers', [UsersDataController::class, 'index']);
    Route::get('/dataControlPago', [UsersDataController::class, 'indexControl']);
    Route::get('/analisisDinero', [AnalisisController::class, 'obtenerAnalisisCredito']);
    Route::get('/analisisMontoPedente', [AnalisisController::class, 'montoPendinteUser']);
    Route::get('/analisisClientes', [AnalisisController::class, 'clientesReprestamos']);
    Route::get('historial/{usuarioId}', [HistorialController::class, 'historial']);

    //add logout
    Route::post('/logout', [AuthController::class, 'logout']);
});





Route::post('/login', [AuthController::class, 'login']);
