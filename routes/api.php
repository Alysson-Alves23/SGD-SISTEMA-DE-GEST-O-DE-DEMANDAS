<?php

use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DemandaController; 
use App\Http\Controllers\UserController; 
use App\Http\Controllers\GrupoController;
use App\Http\Controllers\ChecklistController;
use Illuminate\Support\Facades\Route;


Route::post('/register', [RegisterController::class, 'store']);
Route::post('/login', [LoginController::class, 'store']);


Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy']);
    
    Route::resource('demandas', DemandaController::class);
    
    // Rotas para o checklist
    Route::post('/demandas/{demanda}/checklist', [ChecklistController::class, 'store']);
    Route::put('/demandas/{demanda}/checklist/{item}', [ChecklistController::class, 'update']);
    Route::delete('/demandas/{demanda}/checklist/{item}', [ChecklistController::class, 'destroy']);
    Route::post('/demandas/{demanda}/checklist/reorder', [ChecklistController::class, 'reorder']);
    Route::post('/demandas/{demanda}/checklist/{item}/toggle', [ChecklistController::class, 'toggleConcluido']);
    
    Route::resource('users', UserController::class);

    Route::resource('grupos', GrupoController::class);
});

