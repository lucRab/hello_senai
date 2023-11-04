<?php

namespace App\Http\Controllers;

use App\Http\Requests\DenuciaProjectRequest;
use Auth;
use App\Models\Project;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    private $service;

    public function __construct(
        protected Project $repository
    )
    {
        $this->service = new ProjectService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = $this->repository->with('user')->paginate();
        return ProjectResource::collection($projects);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        if (Auth::guard('sanctum')->check() && $user->tokenCan('project-store'))
        {
            $data = $request->validated();
            $project = tratamentoDados($data);
            $userId = $user->idusuario;
            $slug = $this->service->generateSlug($project['nome_projeto']);

            $project['idusuario'] = $userId;
            $project['slug'] = $slug;
            
            if (!$idprojeto =$this->repository->createProject($project))
                return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
            
            $link = [
                'link' => $data['link'],
                'idprojeto' => $idprojeto
            ];
            if(!$this->repository->linkGit($link)) 
                return response()->json(['message' => 'Não Foi Possível Inserir o Link']);

            return response()->json(['message' => 'Projeto Criado'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $data = $this->service->getProjectBySlug($slug);
        if (!$data)
        {
            return response()->json(['message' => 'Projeto Não Encontrado'], 404);
        }
        return new ProjectResource($data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, $slug)
    {
        $project = $this->service->getProjectBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        
        //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE ATUALIZARÁ
        if (Auth::guard('sanctum')->check() && $user->tokenCan('project-update') && $user->apelido == $project->user->apelido)
        {
            $data = $request->validated();
            $data['idprojeto'] = $project->idprojeto;

            if ($data['nome_projeto'] != $project->nome_projeto)
            {
                $data['slug'] = $this->service->generateSlug($data['nome_projeto']);
            }

            if (!$this->repository->updateProject($data))
            {
                return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
            };       
            return response()->json(['message' => 'Projeto Atualizado'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $project = $this->service->getProjectBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        
        if (Auth::guard('sanctum')->check() && $user->tokenCan('project-update') && $user->apelido == $project->user->apelido)
        {
            if (!$this->repository->deleteProject($project->idprojeto))
            {
                return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
            };       
            return response()->json(['message' => 'Projeto Excluido'], 200);
        }
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function tratamentoDados($data) {
        $tratamento = [
            'nome_projeto'  => $data['nome_projeto'],
            'descricao'     => $data['descricao'],
            'status'        => $data['status'],
        ];
        return $tratamento;
    }

    public function denunciationProject(DenuciaProjectRequest $request) {
        $data = $request->validated();
        if(!$this->repository->denunciaProjeto($data))
        return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);
    }

    public function challengeVinculation(Request $request) {
        $data = $request->all();
        $idProject = $data['idprojeto']; 
        $iddesafio = $data['iddesafio'];
        $get = $this->repository->getProject($idProject);
        if($get[0]['iddesafio'] == null) {
            $data = ['iddesafio' => $iddesafio];
            if(!$this->repository->vinculationChallenge($data, $idProject)) 
            return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403); 
        }else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }

    public function challengeDesvinculation(Request $request) {
        $data = $request->all();
        $idProject = $data['idprojeto']; 
        $get = $this->repository->getProject($idProject);
        if($get[0]['iddesafio'] != null) {
            $data = ['iddesafio' => null];
            if(!$this->repository->vinculationChallenge($data, $idProject)) 
            return response()->json(['message' => 'Não Foi Possível Realizar Essa Ação'], 403);   
        }else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }
}