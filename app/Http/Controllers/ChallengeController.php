<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Models\Challenge;
use App\Models\User;
use App\Services\ChallengeService;
use App\Services\AuthService;
use App\Http\Resources\V1\ChallengeResource;
use App\Services\CustomException;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Validator;

class ChallengeController extends Controller
{
    private $service;
    private $authService;
    
    public function __construct(
        protected Challenge $repository
    ) {
        $this->service = new ChallengeService();
        $this->authService = new AuthService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $challenges = $this->repository->with(['user', 'invitation'])->paginate();
        return ChallengeResource::collection($challenges);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChallengeRequest $request) {
         $user = Auth::guard('sanctum')->user();
         $data = $request->validated();
         try {
            CustomException::authorizedActionException('challenge-create', $user);
            
             $invite = $this->tratamenteDataInvite($data);
             $userId = $user->idusuario;
             $slug = $this->service->generateSlug($data['titulo']);

            $invite['idusuario'] = $userId;
             $invite['slug'] = $slug;
          
             CustomException::actionException($id = $this->repository->createInvitation($invite));
            
             $challege = $this->tratamenteDataChallenge($data, $id, $slug);
             $challege['idprofessor'] = $userId;
             CustomException::actionException($this->repository->createChallenge($challege), 'challenge-create');
        } catch(Exception $e) {
             return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username)
    {
        $getUser = User::where('apelido', '=', $username)->first();
        if (empty($getUser))
        {
            return response()->json(['message' => 'Usuário não encontrado'], 404);
        }
        $userId = $getUser->idusuario;

        if (!$this->authService->isTeacher($userId))
        {
            return response()->json(['message' => 'Usuário não é professor'], 403);
        }
        $challenges = $this->repository
                     ->join('convite', 'desafio.idconvite', '=', 'convite.idconvite')
                     ->where('desafio.idusuario', '=', $userId)
                     ->orderBy('convite.data_convite', 'DESC')
                     ->paginate();
        return ChallengeResource::collection($challenges);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChallengeRequest $request, $slug) {   
        $desafio = $this->service->getBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        try {
            CustomException::authorizedActionException('challenge-update', $user, $desafio);
            
            $data = $request->all();
            $idchallenge = $data['iddesafio'];
            if(!empty($data['imagem'])) {
                $extension = $data['imagem']->getClientOriginalExtension();
                $img = $data['imagem']->storeAs('projects', $data['slug'].'.'.$extension);
            }
            $challenge = $this->tratamenteDataInvite($data);
            
            CustomException::actionException($this->repository->updateChallenge(Auth::guard('sanctum')->id(), $idchallenge,$challenge, $img));
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {
        $user = Auth::guard('sanctum')->user();
        $get = $this->repository->getbySlug($slug);
        try{
            CustomException::authorizedActionException('challenge-delete', $user, $get);
            CustomException::actionException($this->repository->deleteChallenge($get[0]->idconvite,
            $get[0]->idprofessor));
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    public function tratamenteDataInvite($data) {
        $data_invite = [
            'descricao'     => $data['descricao'],
            'titulo'        => $data['titulo']
        ];
        return $data_invite;
    }
    public function tratamenteDataChallenge($data, $idInvite, $name) {
        if(!empty($data['imagem'])) {
            $extension = $data['imagem']->getClientOriginalExtension();
            $imagem = $data['imagem']->storeAs('projects', $name.'.'.$extension);
        }else {
            $imagem = null;
        }
        $data_challerge = [
            'idconvite'     => $idInvite,
            'imagem'  => $imagem
        ];
        return $data_challerge;
    }
}