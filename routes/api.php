<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::resource('usuario',[UserController::class]);

// api/v1/usuario
Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('usuario', UserController::class);
    Route::get('usuario/{username}/projetos', [UserController::class, 'getProjects']);
    Route::get('usuario/{username}/convites', [UserController::class, 'getInvites']);

    Route::apiResource('professor', TeacherController::class);

    Route::apiResource('projeto', ProjectController::class);
    Route::post('projeto/denucia',[ProjectController::class,'denunciationProject'])->name('projeto.denucia');
    Route::post('projeto/desafio',[ProjectController::class,'challengeVinculation'])->name('projeto.desafio');
    Route::post('projeto/desafio/desvincular',[ProjectController::class,'challengeDesvinculation'])->name('projeto.desvicular'); 
    
    Route::apiResource('convite', InvitationController::class);    

    Route::apiResource('desafio', ChallengeController::class);
    Route::put('desafio/{desafio}', [ChallengeController::class,'update'])->name('desafio.update');
    Route::delete('desafio/{desafio}', [ChallengeController::class,'destroy'])->name('desafio.delete'); 
    Route::apiResource('comentario', CommentController::class);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);   
    Route::post('/logout', [AuthController::class, 'logout']);   
    Route::post('/create/teacher', [AuthController::class, 'registerTeacher']);   
    Route::get('/profile', [AuthController::class, 'profile']);   
});

Route::post('/teste/{convite}', [InvitationController::class,'aceitarInvite']);