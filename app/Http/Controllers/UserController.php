<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Project;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\ProjectResource;
use App\Http\Resources\V1\InvitationResource;
use Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private $project;
    public function __construct(
        protected User $repository,
    ) {
        $this->middleware('auth:sanctum')->only(['update', 'destroy']);
        $this->project = new Project();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $searchQuery = $request->query('user');
        $searchQueryLimit = $request->query('limit') ?: 7;
        if (!empty($searchQuery)) {
            $users = $this->repository->where('apelido', 'LIKE', '%' . $searchQuery . '%')->limit($searchQueryLimit)->get();
            return UserResource::collection($users);
        }
        $users = $this->repository->paginate($searchQueryLimit);
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
    public function update(UpdateUserRequest $request, string $username)
    {
        $actualUser = $this->repository->getByNickname($username);
        if (empty($actualUser)) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->idusuario == $actualUser->idusuario) {
            $data = $request->validated();
            if ($actualUser->status !== 'ativo') {
                throw new NotFoundHttpException();
            }
            $tt = $this->repository->updateUser($data, $actualUser->idusuario);
            return response()->json(['message' => 'Dados atualizados'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $id = $user->id;
        $delete = $this->repository->desativateUser($id);
    }

    public function vericationStatus(string $apelido)
    {
        $get = $this->repository->getByNickname($apelido);
        if($get[0]['status'] == 'Inativo') {
            return false;
        }
        return true;
    }

    public function getProjects($username)
    {   
        $user = User::where('apelido', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        $projects = $user->project()->with(['participants', 'user'])->paginate();
        return ProjectResource::collection($projects);
    }

    public function getInvites($username)
    {   
        $user = User::where('apelido', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        $invites = $user->invite()->with('user')->paginate();
        return InvitationResource::collection($invites);
    }
    
    public function saveImg($imagem) {
        //pega a extenção da imegem
        $extension = $imagem->getClientOriginalExtension();
        //salva a imagem e pega o caminho onde ela foi salva
        $img = Storage::disk('public')->putFile('user', $imagem);
        return $img;
    }
}