<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
/**
 * Classe de controlle do usuario
 * @todo Aplicar o try - catha
 */
class UserController extends Controller
{

    public function __construct(
        protected User $repository,
    ) {}

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
    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();
        $data['senha'] = bcrypt($request->senha);
        
        $this->repository->createUser($data);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $apelido)
    { 
        $user = $this->repository->findOrFail($apelido);
        return new UserResource($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $user = $this->repository->findOrFail($id);
        $data = $request->validated();
        if ($user->status != 'ativo') throw new NotFoundHttpException;
        if ($request->senha)
        {
            $data['senha'] = \bcrypt($request->password);
        }
        if ($request->nome)
        {
            $this->repository->generateUsername($data['nome'], $user->idusuario);
        }
        $user->update($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $delete = $this->repository->desativateUser($id);
    }

    public function storeProfessor(StoreUserRequest $request) {
        $data = $request->validated();
        $data['senha'] = bcrypt($request->senha);
        
        $idusuario = $this->repository->createUser($data);
        $this->repository->createProfessor($idusuario);
    }
}
