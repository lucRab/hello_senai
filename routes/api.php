<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChallengeController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\DenounceController;
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
    Route::put('usuario/senha/modificar', [UserController::class, 'changePassoword']);
    Route::put('usuario/conta/desativar', [UserController::class, 'disableAccount']);
    Route::get('usuario/desafios/realizados', [UserController::class, 'getChallengesPerfomed']);
    Route::get('usuario/convites/notificacoes', [UserController::class, 'getNotifications']);
    Route::put('avatar', [UserController::class, 'avatar']);

    Route::apiResource('professor', TeacherController::class);

    Route::apiResource('projeto', ProjectController::class);
    Route::post('projeto/{slug}/comentario', [ProjectController::class, 'comment']);
    Route::post('projeto/{slug}/denuncia', [ProjectController::class,'report']);
    
    Route::apiResource('convite', InvitationController::class);    

    Route::apiResource('desafio', ChallengeController::class);
    Route::put('desafio/{desafio}', [ChallengeController::class,'update'])->name('desafio.update');
    Route::delete('desafio/{desafio}', [ChallengeController::class,'destroy'])->name('desafio.delete'); 

    Route::apiResource('denuncia', DenounceController::class);

    Route::apiResource('comentario', CommentController::class);
});

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);   
    Route::post('/logout', [AuthController::class, 'logout']);   
    Route::post('/create/teacher', [AuthController::class, 'registerTeacher']);   
    Route::get('/profile', [AuthController::class, 'profile']);   
});

//Rotas para email
//api/email/
Route::group(['prefix' => 'email'], function () {
    Route::post('/{convite}', [InvitationController::class, 'acceptInvite']);
    Route::get('/{email}', [InvitationController::class, 'ownerInviteAcceptUser']);
});

Route::post('/teste/{convite}', [InvitationController::class,'aceitarInvite']);