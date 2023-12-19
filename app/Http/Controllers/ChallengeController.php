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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Support\Facades\Storage;

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
    public function index(Request $request)
    {
        $limit = $request->query('limit') ?? 15;
        $challenges = $this->repository->with(['user'])->orderBy('data_criacao', 'DESC')->paginate($limit);
        return ChallengeResource::collection($challenges);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreChallengeRequest $request)
    {
        try {
            if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('challenge-store')) {
                $user = Auth::guard('sanctum')->user();
                $data = $request->validated();

                $userId = $user->idusuario;
                $slug = $this->service->generateSlug($data['titulo']);
                $data['slug'] = $slug;
                $data['idusuario'] = $userId;
                
                if(!empty($data['imagem'])) {
                    $image = Storage::disk('public')->putFile('challenges', $data['imagem']);
                    $data['imagem'] = $image;
                }

                $this->repository->createChallenge($data);
                return response()->json(['message' => 'Desafio criado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch(HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug)
    {
        try {
            $data = $this->service->getBySlug($slug);
            if (!$data) throw new HttpException(404, 'Desafio não encontrado');
            return new ChallengeResource($data);
        } catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateChallengeRequest $request, $slug)
    {
        try {
            if (Auth::guard('sanctum')->check() && Auth::guard('sanctum')->user()->tokenCan('challenge-update')) {
                $user = Auth::guard('sanctum')->user();
                $challenge = $this->service->getBySlug($slug);
                
                if ($challenge->idusuario !== $user->idusuario) {
                    throw new HttpException(401, 'Autorização negada');
                }

                $data = $request->validated();
                $idChallenge = $challenge->iddesafio;
                
                if ($data['titulo'] !== $challenge->titulo) {
                    $data['slug'] = $this->service->generateSlug($data['titulo']);
                }

                if(!empty($data['imagem'])) {
                    $image = Storage::disk('public')->putFile('challenges', $data['imagem']);
                    $data['imagem'] = $image;
                }

                $this->repository->updateChallenge($data, $idChallenge);
                return response()->json(['message' => 'Desafio atualizado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        } catch(HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $challenge = $this->service->getbySlug($slug);
                if ($user->idusuario === $challenge->idusuario && $user->tokenCan('challenge-destroy')) {
                   $this->repository->deleteChallenge($challenge->iddesafio);
                   return response()->json(['message' => 'Desafio excluido'], 200);
                }
            }
            throw new HttpException(401, 'Autorização negada');
        } catch(HttpException $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }

    public function processingDataInvite($data)
    {
        $dataInvite = [
            'descricao'     => $data['descricao'],
            'titulo'        => $data['titulo']
        ];
        return $dataInvite;
    }
    public function processingDataChallenge($data, $idInvite, $name)
    {
        $dataChallenge = [
            'idconvite' => $idInvite,
        ];
        
        if(!empty($data['imagem'])) {
            $extension = $data['imagem']->getClientOriginalExtension();
            $imagem = $data['imagem']->storeAs('projects', $name . '.' . $extension);
            $dataChallenge['imagem'] = $imagem;
        } 
        
        return $dataChallenge;
    }
}