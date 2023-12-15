<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportProject;
use App\Models\User;
use App\Services\CustomException;
use Auth;
use App\Models\Project;
use App\Models\Challenge;
use App\Models\Comment;
use App\Services\ProjectService;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Resources\V1\ProjectResource;
use App\Http\Resources\V1\CommentResource;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Filters\V1\ProjectsFilter;

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
    ) {
        $this->service = new ProjectService();
        $this->users = new User();
        $this->challenge = new Challenge();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filter = new ProjectsFilter();
        $queryItems = $filter->transform($request);
        $order = $request->query('order') ?? 'DESC';
        $limit = $request->query('limit') ?? 7;

        if (empty($queryItems)) {
            $projects = $this->repository->with(['user', 'comments.user', 'comments.reply.user'])->where('status', '1')->orderBy('data_projeto', $order)->paginate($limit);
            return ProjectResource::collection($projects);
        }

        $projects = $this->repository->with(['user', 'comments.user', 'comments.reply.user'])->where($queryItems)->where('status', '1')->orderBy('data_projeto', $order)->paginate($limit);
        return ProjectResource::collection($projects->appends($request->query()));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                //valida os dados recebido
                $data = $request->validated();
                //clona os dados recebidos
                $dataClone = $data;
                //retira os dados dos participantes para fazer o insert no banco
                if (!empty($data['participantes'])) {
                    unset($data['participantes']);
                }
                //cria um apelido
                $slug = $this->service->generateSlug($data['nomeProjeto']);
                $data['slug'] = $slug;
                //trata os dados
                $project = $this->processingData($data, $slug);
                $project['idusuario'] = $user->idusuario;

                
                if (!empty($data['desafio'])) {
                    $challenge = $this->challenge->getBySlug($data['desafio']);
                    if (!$challenge) {
                        return response()->json(['message' => 'Desafio não encontrado'], 404);
                    }
                    $project['iddesafio'] = $challenge->iddesafio;
                }
                
                $projectId = $this->repository->createProject($project);
                if (!empty($dataClone['participantes'])) {
                    $participants = $dataClone['participantes'];
                    try {
                        //adiciona  os participantes participantes
                        $dataParticipants = $this->addParticipants($participants, $projectId);
                        $this->repository->addRangeParticipants($dataParticipants);
                    } catch (HttpException $message) {
                        return response()->json(['message' => $message->getMessage()], 404);
                    }
                }
                return response()->json(['message' => 'Projeto Criado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($slug)
    {
        try {
            $data = $this->service->getBySlug($slug);
            if (!$data) throw new HttpException(404, 'Projeto não encontrado');
            if ($data->status === 0) {
                if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->idusuario === $data->idusuario) {
                    return new ProjectResource($data);
                }
                throw new HttpException(404, 'Projeto não encontrado');
            }
            return new ProjectResource($data);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, string $slug)
    {
        try {
            $user = Auth::guard('sanctum')->user();
            $project = $this->service->getBySlug($slug);

            if (empty($project)) {
                throw new HttpException(404, 'Projeto não encontrado');
            }

            CustomException::authorizedActionException('project-update', $user, $project);
            $data = $request->validated();
            $data['idprojeto'] = $project->idprojeto;

            if ($data['nomeProjeto'] !== $project->nome_projeto) {
                $data['slug'] = $this->service->generateSlug($data['nomeProjeto']);
            }

            $dataUpdated = $this->processingData($data);
            $dataUpdated['idprojeto'] = $data['idprojeto'];

            if ($request->participantes) {
                $participantsNotInNewRequest = $this->updateParticipants($project->participants, $request->participantes, $data['idprojeto']);
            }

            $this->repository->updateProject($dataUpdated);
            return response()->json($data, 200);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function disable($slug) {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $project = $this->service->getBySlug($slug);
                if (!$project) throw new HttpException(404, 'Projeto não encontrado');
                if ($project->idusuario === $user->idusuario) {
                    CustomException::authorizedActionException('project-destroy', $user, $project);
                    $this->repository->disableProject($project->idprojeto);
                    return response()->json(['message' => 'Projeto Desativado'], 200);
                }
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    public function restore($slug) {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $project = $this->service->getBySlug($slug);
                if (!$project) throw new HttpException(404, 'Projeto não encontrado');
                if ($project->status === 1) throw new HttpException(401, 'O projeto não está desativado');
                if ($project->idusuario === $user->idusuario) {
                    CustomException::authorizedActionException('project-update', $user, $project);
                    $this->repository->restoreProject($project->idprojeto);
                    return response()->json(['message' => 'Projeto Reativado'], 200);
                }
            }
            throw new HttpException(401, 'Autorização negada');
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    private function processingData($data)
    {
        //cria o array somento com os dados para cria o projeto
        $dataProject = [
            'nome_projeto'  => $data['nomeProjeto'],
            'descricao'     => $data['descricao'],
            'projeto_status'        => $data['projetoStatus'],
            'github' => $data['github']
        ];

        if (!empty($data['slug'])) {
            $dataProject['slug'] = $data['slug'];
        }

        //verifica se a uma imagem nos dados enviado
        if(gettype($data['imagem']) !== 'string') {
            $extension = $data['imagem']->getClientOriginalExtension();
            $image = Storage::disk('public')->putFile('projects', $data['imagem']);
            $dataProject['imagem'] = $image;
        }
        return $dataProject;
    }

    public function comment(StoreCommentRequest $request, $slug)
    {
        if (Auth::guard('sanctum')->check()) {
            $userId = Auth::guard('sanctum')->user()->idusuario;
            $data = $request->validated();
            $project = $this->service->getBySlug($slug);
            if (!$project) {
                return response()->json(['message' => 'Projeto não encontrado'], 404);
            }
            $commentData = [
                'idusuario' => $userId,
                'idprojeto' => $project->idprojeto,
                'texto' => $data['texto'],
                'criado_em' => Carbon::now(new \DateTimeZone('America/Sao_Paulo'))
            ];

            if (empty($data['comentarioPai'])) {
                $comment = new Comment($commentData);
                $comment->save();
            } else {
                $parentComment = Comment::find($data['comentarioPai']);
                if (!$parentComment) {
                    return response()->json(['message' => 'Comentário não encontrado'], 404);
                }
                $comment = new Comment($commentData);
                $comment->save();
                $updatedComment = $parentComment->update(['idresposta' => $comment->idcomentario]);
                if (!$updatedComment) {
                    return response()->json(['message' => 'Não foi possivel realizar o comentário'], 403);
                }
                $commentData['comentarioPai'] = $data['comentarioPai'];
            }

            return response()->json(['data' => $commentData], 200);
        }
        return response()->json(['message' => 'Autorização negada'], 401);
    }

    public function report(StoreReportProject $request, $slug)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $project = $this->service->isProjectStored($slug);
                if (!$project) {
                    return response()->json(['message' => 'Projeto não encontrado'], 404);
                }
                //valida os dados recebidos
                $data = $request->validated();
                $data['idprojeto'] = $project->idprojeto;
                //verifica se a ação feita não deu erro
                CustomException::actionException($this->repository->reportProject($data));
                return response()->json(['message' => 'Denuncia realizada'], 200);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
        return response()->json(['message' => 'Autorização negada'], 401);
    }

    private function addParticipants($participants, $projectId)
    {
        //decodifica os dados enviados
        $participants = json_decode($participants);
        //verifica os dados no array
        foreach ($participants as $key => $value) {
            //faz o select dos nickname dos participantes
            $nickname = $this->users->getByNickname($value);
            //verifica se exite o nickname
            if (!$nickname) {
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

    public function updateParticipants($olderParticipants, $newParticipants, $projectId)
    {
        $olderParticipantsUsernames = $olderParticipants->map(function ($participant) {
            return $participant->apelido;
        })->toArray();

        $newParticipantsUsername = \json_decode($newParticipants);

        // Find participants to remove
        $participantsToRemove = \array_diff($olderParticipantsUsernames, $newParticipantsUsername);

        // Find participants to add
        $participantsToAdd = \array_diff($newParticipantsUsername, $olderParticipantsUsernames);

        // Remove participants
        if (!empty($participantsToRemove)) {
            $participantsToRemoveIds = $this->users->whereIn('apelido', $participantsToRemove)->pluck('idusuario');
            if (!DB::table('permissao')->whereIn('idusuario', $participantsToRemoveIds)->delete()) {
                throw new HttpException(403, 'Não foi possível remover o participante');
            };
        }

        // Add participants
        if (!empty($participantsToAdd)) {
            $participantsToAddIds = $this->users->whereIn('apelido', $participantsToAdd)->pluck('idusuario');
            $newPermissions = array_map(function ($id) use ($projectId) {
                return ['idusuario' => $id, 'idprojeto' => $projectId];
            }, $participantsToAddIds->toArray());
            DB::table('permissao')->insert($newPermissions);
        }

        return true;
    }


    public function challengeVinculation(Request $request)
    {
        //pega os dados recebido
        $data = $request->all();
        $idProject = $data['idprojeto'];
        $iddesafio = $data['iddesafio'];
        //faz do select do projeto que quer ser vinculado
        $get = $this->repository->getProject($idProject);
        //verifica se o projeto já estar vinculado a um desafio
        if($get[0]['iddesafio'] == null) {
            $data = ['iddesafio' => $iddesafio];
            try {
                //verifica se a ação feita não deu erro
                CustomException::actionException($this->repository->vinculationChallenge($data, $idProject));
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        } else {
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }

    public function challengeDesvinculation(Request $request)
    {
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
            } catch (\Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        } else {
            return response()->json(['message' => 'Esse projeto já estar vinculado a um desafio'], 403);
        }
    }
}