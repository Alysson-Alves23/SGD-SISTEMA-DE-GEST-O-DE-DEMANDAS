<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DemandaController; 
use App\Http\Controllers\UserController; 
use App\Http\Controllers\GrupoController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy']);
    
    Route::resource('demandas', DemandaController::class);
    
    Route::resource('users', UserController::class);

    Route::resource('grupos', GrupoController::class);
});

