<?php

namespace App\Http\Controllers;

use App\Http\Requests\DenuciaProjectRequest;
use App\Services\CustomException;
use Auth;
use App\Models\Project;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    private $service;
    /**
     * Método construtor
     *
     * @param Project $repository
     */
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
        Log::info(self::class. ' Requisição:: Create Projeto', [ 'usuario' => $user,'dados' => $request->all()]);
        try {
            CustomException::authorizedActionException('project-store', $user);
            $data = $request->validated();
            $project = $this->tratamentoDados($data);
            $userId = $user->idusuario;
            $slug = $this->service->generateSlug($project['nome_projeto']);

            $project['idusuario'] = $userId;
            $project['slug'] = $slug;

            
            CustomException::actionException($idprojeto =$this->repository->createProject($project));
            
            $link = [
                'link' => $data['link'],
                'idprojeto' => $idprojeto
            ];
            CustomException::actionException($this->repository->linkGit($link)); 
            return response()->json(['message' => 'Projeto Criado'], 200); 
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        $data = $this->service->getBySlug($slug);
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
        $project = $this->service->getBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        
        try {     
            //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE ATUALIZARÁ
            CustomException::authorizedActionException( 'project-update', $user, $project);
        
            $data = $request->validated();
            $data['idprojeto'] = $project->idprojeto;

            if ($data['nome_projeto'] != $project->nome_projeto)
            {
                $data['slug'] = $this->service->generateSlug($data['nome_projeto']);
            }
            CustomException::actionException($this->repository->updateProject($data));
            return response()->json(['message' => 'Projeto Atualizado'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $project = $this->service->getBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        try{
            CustomException::authorizedActionException('project-update', $user, $project);
            CustomException::actionException($this->repository->deleteProject($project->idprojeto));
            return response()->json(['message' => 'Projeto Excluido'], 200);
            
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }       
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
        try{
            CustomException::actionException($this->repository->denunciaProjeto($data));
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    public function challengeVinculation(Request $request) {
        $data = $request->all();
        $idProject = $data['idprojeto']; 
        $iddesafio = $data['iddesafio'];
        $get = $this->repository->getProject($idProject);
        if($get[0]['iddesafio'] == null) {
            $data = ['iddesafio' => $iddesafio];
            try{
                CustomException::actionException($this->repository->vinculationChallenge($data, $idProject));
            }catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403); 
            }
        } else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }

    public function challengeDesvinculation(Request $request) {
        $data = $request->all();
        $idProject = $data['idprojeto']; 
        $get = $this->repository->getProject($idProject);
        if($get[0]['iddesafio'] != null) {
            $data = ['iddesafio' => null];
            try {
                CustomException::actionException($this->repository->vinculationChallenge($data, $idProject));
            }catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403); 
            } 
        }else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }
}