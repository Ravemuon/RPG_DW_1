<?php

use App\Http\Controllers\{
    ClasseController,
    PersonagemController,
    MissaoController,
    UsuarioController
};

Route::get('/', fn() => view('welcome'))->name('home');

// LOGIN
Route::get('/login', [UsuarioController::class, 'loginForm'])->name('login');
Route::post('/login', [UsuarioController::class, 'login']);
Route::get('/logout', [UsuarioController::class, 'logout'])->name('logout');

// CRUDS
Route::resource('classes', ClasseController::class);
Route::resource('personagens', PersonagemController::class);
Route::resource('missoes', MissaoController::class);
