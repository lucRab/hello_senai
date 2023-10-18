<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use Illuminate\Support\Facades\DB;

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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function show(User $usuario)
    { 
        return new UserResource($usuario);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, string $id)
    {
        $update = $this->repository->desativateUser($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        //
    }
}
