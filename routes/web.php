<?php

use Illuminate\Support\Facades\Route;

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
    return view('welcome');
});

// NAVEGAÇÃO DA DOCUMENTAÇÃO
/**
 * Layout inicial
 */
Route::get('/documentation', function() {
    return view('layouts/documentation');
});

/**
 * Pagina Inicial
 */
Route::get('/documentation/inicio', function() {
    return view('pages/documentation/inicio/inicio');
});

/**
 * Pagina com informações sobre Usuário
 */
Route::get('/documentation/usuario', function() {
    return view('pages/documentation/usuario/usuario');
});

/**
 * Pagina com informações sobre Convite
 */
Route::get('/documentation/convite', function() {
    return view('pages/documentation/convite/convite');
});

/**
 * Pagina com informações sobre Projeto
 */
Route::get('/documentation/projeto', function() {
    return view('pages/documentation/projeto/projeto');
});