<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreChallengeRequest;
use App\Http\Requests\UpdateChallengeRequest;
use App\Http\Resources\v1\ChallengeResource;
use App\Models\Challenge;
use App\Services\ChallengeService;
use App\Services\CustomException;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Storage;
use Validator;

class ChallengeController extends Controller
{
    private $service;
    
    public function __construct(
        protected Challenge $repository
    ) {
        $this->service = new ChallengeService();
    }
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $challenge = $this->repository->with(['user', 'invitation'])->paginate();
        return ChallengeResource::collection($challenge);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChallengeRequest $request) {
        //Pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        
         try {
            //verifica se o usuario tem autorização para realizar essa ação
            CustomException::authorizedActionException('challenge-create', $user);
            //valida os dados recebidos
            $data = $request->validated();
            
            $slug = $this->service->generateSlug($data['titulo']);//cria um apelido
            $challege = $this->tratamenteDataChallenge($data, $slug, $user->idusuario); //trata os dados para criar o desafio

            //verifica se a ação feita não gerou um erro
            CustomException::actionException($this->repository->createChallenge($challege), 'challenge-create');
        } catch(Exception $e) {
             return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Challenge $challenge)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Challenge $challenge)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChallengeRequest $request, $slug) {   
        //Pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        try {
            $desafio = $this->service->getBySlug($slug);//pega o apelido do desafio
            //verifica se o usuario tem autorização para realizar essa ação
            CustomException::authorizedActionException('challenge-update', $user, $desafio);
            //valida os dados recebidos
            $data = $request->validated();
            $idchallenge = $data['iddesafio'];
            //verifica se a uma imagem nos dados enviado
            if(!empty($data['imagem'])) {
                //salva a imagem e pega o caminho onde ela foi salva
                $img = Storage::disk('public')->putFile('challenge', $data['imagem']);
            }
            //trata os dados para atualizar o desafio
            $challenge = $this->tratamenteDataInvite($data);
            //verifica se a ação feita não gerou um erro
            CustomException::actionException($this->repository->updateChallenge(Auth::guard('sanctum')->id(), $idchallenge,$challenge, $img));
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {
        //Pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        try{
            $get = $this->repository->getbySlug($slug);//pega o apelido do desafio
            //verifica se o usuario tem autorização para realizar essa ação
            CustomException::authorizedActionException('challenge-delete', $user, $get);
            //verifica se a ação feita não gerou um erro
            CustomException::actionException($this->repository->deleteChallenge($get[0]->idconvite,
            $get[0]->idprofessor));
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }
    
    private function tratamenteDataInvite($data) {
        //cria o array somento com os dados para cria a base do desafio
        $data_invite = [
            'descricao'     => $data['descricao'],
            'titulo'        => $data['titulo']
        ];
        return $data_invite;
    }
    private function tratamenteDataChallenge($data, $name, $idusuario ) {
        //verifica se a uma imagem nos dados enviado
        if(!empty($data['imagem'])) {
            //pega a extenção da imegem
            $extension = $data['imagem']->getClientOriginalExtension();
            //salva a imagem e pega o caminho onde ela foi salva
            $imagem = $data['imagem']->storeAs('projects', $name.'.'.$extension);
        }else {
            //caso não tenha imagem ele define como nulo
            $imagem = null;
        }
        //cria o array somento com os dados para cria o desafio
        $data_challerge = [
            'idusuario'     => $idusuario,
            'imagem'        => $imagem,
            'descricao'     => $data['descricao'],
            'titulo'        => $data['titulo'],
            'slug'          => $name
        ];
        return $data_challerge;
    }
}
