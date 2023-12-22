<?php

namespace App\Http\Controllers;

use App\Models\Adm;
use App\Http\Requests\StoreAdmRequest;
use App\Http\Requests\UpdateAdmRequest;
use App\Models\User;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Auth;

class AdmController extends Controller
{
    private $user;
    public function __construct(
        protected Adm $repository
    )
    {
        $this->user = new User();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('adm-store')) {
            
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAdmRequest $request)
    {
        try {
            $data = $request->validated();
            $data['senha'] = bcrypt($request->senha);
            if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('project-store')) {
                $idUser = $this->user->createUser($data);
                $this->repository->createAdm($idUser->idusuario);
                return response()->json(['message' => 'Adm registrado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }
}