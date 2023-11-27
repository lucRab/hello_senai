<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function __construct(
        protected User $repository,
    ) 
    {
        $this->middleware('auth:sanctum')->only(['update', 'destroy']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = $this->repository->paginate();
        return UserResource::collection($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request) {
        //valida os dados recebidos
        $data = $request->validated();
        try {
            //encripta da senha
            $data['senha'] = bcrypt($request->senha);
            //tenta cria o usuario
            $this->repository->createUser($data);
        }catch (NotFoundHttpException $e) {
            Log::error($e->getMessage());
            return response()->json(['message' => $e->getMessage()], 403);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $apelido) { 
        $user = $this->repository->findOrFail($apelido);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id) {
        $user = $this->repository->findOrFail($id);
        //valida os dados recebidos
        $data = $request->validated();
        //verifica se o usuario estar ativo
        if ($user->status != 'ativo') throw new NotFoundHttpException;
        //verifica se o usuario vai atualizar a senha
        if ($request->senha) {
            //encripta da senha
            $data['senha'] = \bcrypt($request->password);
        }
        //verifica se o usuario vai atualizar o nome
        if ($request->nome) {
            //gera um apelido a partir do nome
            $this->repository->generateUsername($data['nome'], $user->idusuario);
        }
        //tenta atualizar o usuario
        $user->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) {
        //tenta desativar o usuario
        $delete = $this->repository->desativateUser($user->id);
    }
    public function vericationStatus(string $apelido) {
        $get = $this->repository->getByNickname($apelido);
        if($get[0]['status'] == 'Inativo') return false;
        return true;
    }
}