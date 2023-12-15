<?php

namespace App\Http\Controllers;

use App\Services\CustomException;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTeacherRequest;
use App\Http\Requests\UpdateTeacherRequest;
use App\Http\Resources\V1\TeacherResource;
use App\Http\Resources\V1\ChallengeResource;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Challenge;
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Http\Resources\V1\UserResource;
use App\Services\AuthService;

class TeacherController extends Controller
{
    private $user;
    private $authService;

    public function __construct(
        protected Teacher $repository
    )
    {
        $this->middleware('auth:sanctum')->only(['unauthenticatedTeachers', 'authenticate']);
        $this->user = new User();
        $this->authService = new AuthService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = $this->repository->with(['challenge' => function($query) {
            $query->take(3);
        }, 'user'])->paginate();
        return TeacherResource::collection($teachers);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTeacherRequest $request)
    {   
        try {
            $data = $request->validated();
            $data['senha'] = bcrypt($request->senha);
            $idUser = $this->user->createUser($data);
            $this->repository->createTeacher($idUser);

            return response()->json(['message' => 'Registro feito com sucesso', 200]);
        }catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode()); 
        } 
    }

    public function unauthenticatedTeachers() 
    {
        try {
            if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('project-store')) {
                $teachers = $this->repository
                ->where('autenticado', '0')
                ->join('usuario', 'professor.idusuario', '=', 'usuario.idusuario')
                ->select('usuario.*')
                ->get();

                return UserResource::collection($teachers);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function authenticate(UpdateTeacherRequest $request)
    {
        $data = $request->validated();
        try {
            if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('project-store')) {
                $user = User::where('email', $data['email'])->first();
                if (!$user) throw new HttpException(404, 'Usuário não encontrado');
                $this->repository->authorizeTeacher($user->idusuario);
                return response()->json(['message' => 'Professor autenticado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function getChallenges(string $username)
    {
        $getUser = User::where('apelido', '=', $username)->first();
        if (empty($getUser)) {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        $userId = $getUser->idusuario;

        if (!$this->authService->isTeacher($userId)) {
            return response()->json(['message' => 'Usuário não é professor'], 403);
        }
        $challenges = Challenge::with('user')->where('desafio.idusuario', '=', $userId)
        ->orderBy('desafio.data_criacao', 'DESC')
        ->paginate();
        return ChallengeResource::collection($challenges);
    }
}