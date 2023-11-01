<?php

use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::resource('usuario',[UserController::class]);

// api/v1/usuario
Route::group(['prefix' => 'v1'], function () {
    Route::apiResource('usuario', UserController::class);
    Route::post('usuario/professor', [UserController::class,'storeProfessor'])->name('usuario.professor');
    Route::apiResource('projeto', ProjectController::class);
    Route::apiResource('convite', InvitationController::class);    
    Route::apiResource('desafio', ChallengeController::class);
    Route::put('desafio/{professor}/{desafio}', [ChallengeController::class,'update'])->name('desafio.update');
    Route::delete('desafio/{professor}/{desafio}', [ChallengeController::class,'destroy'])->name('desafio.delete'); 
    Route::apiResource('comentario', CommentController::class);
});
