<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Services\AuthService;
use App\Http\Resources\V1\UserResource;
use App\Http\Requests\StoreTeacherRequest;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;


class AuthController extends Controller
{
    private $service;
    
    
    public function __construct(
        protected User $repository
    )
    {      
        $this->service = new AuthService();
    }
    
    public function getAbilities()
    {
        return ['project-store', 'project-update', 'project-destroy', 'invite-store', 'invite-update', 'invite-destroy'];
    }



    public function registerTeacher(StoreTeacherRequest $request)
    {
        Log::info(self::class. ' Requisição de registro de professor', ['dados' => $request->all()]);
        if (!Auth::guard('sanctum')->user()->tokenCan('teacher-store'))
        {
            return response()->json("Unauthorized", 401);
        }
        $data = $request->validated();
        $data['senha'] = bcrypt($request->senha);
        $idUser = $this->repository->createUser($data);
    }

    public function login(Request $request)
    {
        try {
            Log::info(self::class. ' Requisição de login', ['dados' => $request->only('email')]);
            $credentials = Auth::attempt($request->only('email', 'senha'));
            $abilities = $this->getAbilities();
            
            if ($credentials)
            {
                $userId = Auth::user()->idusuario;
                $status = $request->user()->status;
                
                if ($this->service->isAdm($userId))
                {
                    Log::info(self::class. " Login realizado",['dados' => $request->all(),
                    "browser" => $_SERVER["HTTP_USER_AGENT"],
                    'URI' => $_SERVER["REQUEST_URI"],
                    'Server' => $_SERVER["SERVER_SOFTWARE"]]);
                    if ($status == 'inativo') {
                        $request->user()->update(['status' => 'ativo']);
                    }
                    return $request->user()->createToken('token');
                }
                else
                {
                    if ($teacher = $this->service->isTeacher($userId))
                    {
                        $abilities[] = 'challenge-store';
                        $abilities[] = 'challenge-update';
                        $abilities[] = 'challenge-destroy';
                        if ($teacher->autenticado === 0) {
                            throw new HttpException(401, 'Professor ainda não autenticado');
                        }
                    }
                    Log::info(self::class. " Login realizado",['dados' => $request->all(),
                    "browser" => $_SERVER["HTTP_USER_AGENT"],
                    'URI' => $_SERVER["REQUEST_URI"],
                    'Server' => $_SERVER["SERVER_SOFTWARE"]]);
                    if ($status == 'inativo') {
                        $request->user()->update(['status' => 'ativo']);
                    }
                    return $request->user()->createToken('token', $abilities);
                }
                throw new HttpException(403, 'Dados Incorretos');
            }
        } catch (HttpException $e) {
            Log::error(self::class. " Error Login",['dados' => $request->all(),
            "browser" => $_SERVER["HTTP_USER_AGENT"],
            'URI' => $_SERVER["REQUEST_URI"],
            'Server' => $_SERVER["SERVER_SOFTWARE"]]);
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
        
    }

    public function profile()
    {
        if (Auth::guard('sanctum')->check())
        {
            $data = new UserResource(Auth::guard('sanctum')->user());
            return response()->json($data, 200);
        }
        return response()->json(['message' => "Autorização negada"], 401);
    }

    public static function logout()
    {
        if (Auth::guard('sanctum')->check()) {
            Auth::guard('sanctum')->user()->currentAccessToken()->delete(); 
            return response()->json(['message' => 'Token Revogado'], 200);
        }
        return response()->json(['message' => 'Autorização negada'], 401);
    }
    
}