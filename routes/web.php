<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageController;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', [PageController::class, 'showLogin'])->name('login');
Route::get('/cadastro', [PageController::class, 'showRegister']);
Route::get('/dashboard', [PageController::class, 'showDashboard']);
Route::get('/demanda-form', [PageController::class, 'showCreateDemandForm']);
Route::get('/demanda-detalhe', [PageController::class, 'showDemandDetail']);
Route::get('/demanda-update', [PageController::class, 'showEditDemandForm']);