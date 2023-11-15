<?php

namespace App\Http\Controllers;

use App\Services\CustomException;
use Auth;
use App\Http\Resources\V1\InvitationResource;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use App\Models\Invitation;
use App\Services\InvitationService;

class InvitationController extends Controller
{
    private $service;

    public function __construct(
        protected Invitation $repository
    )
    {
        $this->service = new InvitationService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $invitations = $this->repository->with('user')->paginate();
        return InvitationResource::collection($invitations);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvitationRequest $request)
    {
        $user = Auth::guard('sanctum')->user();
        try {
            CustomException::authorizedActionException('invite-store', $user, "InvitationController::store");

            $data = $request->validated();
            $userId = $user->idusuario;
            $slug = $this->service->generateSlug($data['titulo']);

            $data['idusuario'] = $userId;
            $data['slug'] = $slug;
            CustomException::actionException($this->repository->createInvitation($data));  
            return response()->json(['message' => 'Convite Criado'], 200);

        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvitationRequest $request, $slug)
    {
        $invitation = $this->service->getInvitationBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        
        //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE ATUALIZARÁ
        try {
            CustomException::authorizedActionException('invite-update', $user, $invitation);
            $data = $request->validated();
            $data['idconvite'] = $invitation->idconvite;

            if ($data['titulo'] != $invitation->titulo)
            {
                $data['slug'] = $this->service->generateSlug($data['titulo']);
            }
            CustomException::actionException($this->repository->updateInvitation($data));       
            return response()->json(['message' => 'Convite Atualizado'], 200);
        }catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $invitation = $this->service->getInvitationBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        
        try {
            CustomException::authorizedActionException('invite-update', $user, $invitation);
            CustomException::actionException(!$this->repository->deleteInvitation($invitation->idconvite));   

            return response()->json(['message' => 'Convite Excluido'], 200);
        }catch (\Exception $e) {
           return response()->json(['message' => $e->getMessage()], 403); 
        }
    }
}