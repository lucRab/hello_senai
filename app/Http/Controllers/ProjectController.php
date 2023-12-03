<?php

namespace App\Http\Controllers;

use App\Http\Requests\DenuciaProjectRequest;
use App\Models\User;
use App\Services\CustomException;
use Auth;
use App\Models\Project;
use App\Models\Challenge;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\V1\ProjectResource;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    private $service;
    private $users;
    private $challenge;
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
        $this->users = new User();
        $this->challenge = new Challenge();
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
        //pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        try {
            //verifica se o usuario tem autorização para realizar essa ação
            CustomException::authorizedActionException('project-store', $user);

            //valida os dados recebido
            $data = $request->validated();
            //clona os dados recebidos
            $dataClone = $data;
            //retira os dados dos participantes para fazer o insert no banco
            if (!empty($data['participantes'])) unset($data['participantes']);    
            //cria um apelido
            $slug = $this->service->generateSlug($data['nomeProjeto']);

            //trata os dados 
            $project = $this->processingData($data, $slug);
            $project['idusuario'] = $user->idusuario;

            if (!empty($data['desafio']))
            {
                $challenge = $this->challenge->getBySlug($data['desafio']);
                if (!$challenge)
                {
                    return response()->json(['message' => 'Desafio não encontrado'], 404);
                }
                $project['iddesafio'] = $challenge->iddesafio;
            }

            //verifica se a ação feita não deu erro
            CustomException::actionException($projectId = $this->repository->createProject($project));
            //Pega os dados dos participantes do clone dos dados recebidos
            //verifica se a participantes
            if (!empty($dataClone['participantes'])) {
                $participants = $dataClone['participantes'];
                try {
                    //adiciona  os participantes participantes
                    $dataParticipants = $this->addParticipants($participants, $projectId);
                    $this->repository->addRangeParticipants($dataParticipants);
                } catch (\Exception $message) {
                    return response()->json(['message' => $message->getMessage()], 404);
                }
            }
            //cria um array para inserir o link
            $link = [
                'link' => $data['link'],
                'idprojeto' =>$projectId
            ];
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->linkGit($link)); 
            return response()->json(['message' => 'Projeto Criado'], 200); 
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        } 
    }

    /**
     * Display the specified resource.
     */
    public function show($slug) {
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
    public function update(UpdateProjectRequest $request, $slug) {
        //pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        //pega o apelido do projeto
        $project = $this->service->getBySlug($slug);
        
        try {     
            //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE ATUALIZARÁ
            CustomException::authorizedActionException('project-update', $user, $project);
            //valida os dados recebidos
            $data = $request->validated();
            $data['idprojeto'] = $project->idprojeto;
            //verifica se o nome atualizado é o mesmo que foi salvo primeiro
            if ($data['nomeProjeto'] != $project->nome_projeto)
            {
                //cria um apelido
                $data['slug'] = $this->service->generateSlug($data['nome_projeto']);
            }
            //verifica se a uma imagem nos dados enviado
            if($data['imagem']) {
                //pega a extenção da imegem
                $extension = $data['imagem']->getClientOriginalExtension();
                //salva a imagem e pega o caminho onde ela foi salva
                $data['imagem'] = $data['imagem']->storeAs('projects', $project['slug'].'.'.$extension);
            }
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->updateProject($data));
            return response()->json(['message' => 'Projeto Atualizado'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }       
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {
        //pega o usuario logado
        $user = Auth::guard('sanctum')->user();
        //pega o apelido do projeto
        $project = $this->service->getBySlug($slug);
        try{
            //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE DELETARA
            CustomException::authorizedActionException('project-update', $user, $project);
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->deleteProject($project->idprojeto));
            return response()->json(['message' => 'Projeto Excluido'], 200);
            
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }       
    }

    private function processingData($data, $slug) {
         //cria o array somento com os dados para cria o projeto
        $dataProject = [
            'nome_projeto'  => $data['nomeProjeto'],
            'descricao'     => $data['descricao'],
            'status'        => $data['status'],
            'slug'          => $slug,    
        ];
        //verifica se a uma imagem nos dados enviado
        if($data['imagem']) {
            //pega a extenção da imegem
            $extension = $data['imagem']->getClientOriginalExtension();
            $image = Storage::disk('public')->putFile('projects', $data['imagem']);
            //salva a imagem e pega o caminho onde ela foi salva
            $dataProject['imagem'] = $image;
        }
        return $dataProject;
    }

    public function denunciationProject(DenuciaProjectRequest $request) {
        //valida os dados recebidos
        $data = $request->validated();
        try{
            //verifica se a ação feita não deu erro
            CustomException::actionException($this->repository->denunciaProjeto($data));
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }
    private function addParticipants($participants, $projectId)  {
        //decodifica os dados enviados
        $participants = json_decode($participants);
        //verifica os dados no array
        foreach ($participants as $key => $value) {
            //faz o select dos nickname dos participantes
            $nickname = $this->users->getByNickname($value);
            //verifica se exite o nickname
            if (!$nickname)
            {
                throw new HttpException(404, "Usúario Não Encontrado");
            };
            //pega o id do usuario 
            $iduser = $nickname->idusuario;
            //prepara o array para fazer o insert no banco
            $data[] = [
                'idusuario' => $iduser,
                'idprojeto' => $projectId,
                'tipo' => 'leitor'
            ];
        }
        return $data;
    }
    public function challengeVinculation(Request $request) {
        //pega os dados recebido
        $data = $request->all();
        $idProject = $data['idprojeto']; 
        $iddesafio = $data['iddesafio'];
        //faz do select do projeto que quer ser vinculado
        $get = $this->repository->getProject($idProject);
        //verifica se o projeto já estar vinculado a um desafio
        if($get[0]['iddesafio'] == null) {
            $data = ['iddesafio' => $iddesafio];
            try{
                //verifica se a ação feita não deu erro
                CustomException::actionException($this->repository->vinculationChallenge($data, $idProject));
            }catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403); 
            }
        } else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }

    public function challengeDesvinculation(Request $request) {
        //pega os dados recebido
        $data = $request->all();
        $idProject = $data['idprojeto'];
         //faz do select do projeto que quer ser vinculado
        $get = $this->repository->getProject($idProject);
        //verifica se o projeto já estar vinculado a um desafi
        if($get[0]['iddesafio'] != null) {
            $data = ['iddesafio' => null];
            try {
                //verifica se a ação feita não deu erro
                CustomException::actionException($this->repository->vinculationChallenge($data, $idProject));
            }catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403); 
            } 
        }else{
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }
}