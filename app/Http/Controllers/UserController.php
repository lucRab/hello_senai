<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use App\Models\Project;
use App\Models\Challenge;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\V1\UserResource;
use App\Http\Resources\V1\ProjectResource;
use App\Http\Resources\V1\ChallengeResource;
use App\Http\Resources\V1\NotificationsResource;
use App\Http\Resources\V1\InvitationResource;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DateService;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    private $project;
    private $dateService;

    public function __construct(
        protected User $repository,
    ) {
        $this->middleware('auth:sanctum')->only(['update', 'destroy']);
        $this->project = new Project();
        $this->dateService = new DateService();
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
        $user = User::where('apelido', $apelido)->first();
        if (!$user) return response()->json(['message' => 'Usuário não encontrado'], 404);
        $data = [
            'nome' => $user->nome,
            'apelido' => $user->apelido,
            'avatar' => $user->avatar ? Storage::url($user->avatar) : null,
            'criadoEm' => DateService::transformDateHumanReadable($user->data_criacao),
            'status' => $user->status
        ];
        return $data;
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

    public function avatar(Request $request) {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $avatar = $request->validate([
                'avatar' => 'required|image|max:1024'
                ]);
                $savedAvatar = Storage::disk('public')->putFile('avatars', $avatar['avatar']);
                $this->repository->where('idusuario', $user->idusuario)->update(['avatar' => $savedAvatar]);
                return response()->json(['message' => 'Avatar salvo com sucesso'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $th) {
            return response()->json(['message' => $th->getMessage()], $th->getStatusCode());
        }
    }

    public function disableAccount()
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user()->idusuario;
                $delete = $this->repository->disable($user);
                AuthController::logout();
                return response()->json(['message' => 'Conta desativada'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $th) {
            return response()->json(['message' => $th->getMessage()], $th->getStatusCode());
        }
    }

    public function changePassoword(Request $request)
    {
        if (Auth::guard('sanctum')->check()) {
            $validated = $request->validate([
                'senha' => 'required|min:6|max:255'
            ]);
            $user = Auth::guard('sanctum')->user()->idusuario;
            $this->repository->updateUser(['senha' => bcrypt($validated['senha'])], $user);
            return response()->json(['message' => 'Senha atualizada'], 200);
        }
        return response()->json(['message' => 'Autorização negada'], 401);
    }

    public function getProjects($username)
    {   
        $user = User::where('apelido', $username)->first();
        if (!$user) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        $projects = $user->project()->with(['user'])->where('status', '1')->paginate();

        if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->idusuario === $user->idusuario) {
            $projects = $user->project()->with(['user'])->paginate();
        }

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

    public function getChallengesPerfomed(Request $request)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $teacher = $request->query('teacher');
                $idTeacher = $this->repository->getByNickname($teacher);
                if (!$idTeacher) {
                    throw new HttpException(404, 'Professor não encontrado');
                }
                $user = Auth::guard('sanctum')->user()->idusuario;
                $challenges = Challenge::with('project')->where('idusuario', $idTeacher->idusuario)->whereHas('project', function ($query) use ($user) {
                    $query->where('idusuario', $user);
                })->get();
                return ChallengeResource::collection($challenges);
            }
        } catch (HttpException $th) {
            return response()->json(['message' => $th->getMessage()], $th->getStatusCode());
        }
        
    }

    public function getNotifications() 
    {
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user()->idusuario;
            $notifications = $this->repository->notifications($user);
            return NotificationsResource::collection($notifications);
        }
    }
}