<?php

namespace App\Http\Controllers;

use App\Services\CustomException;
use App\Services\Mails;
use Auth;
use App\Http\Resources\V1\InvitationResource;
use App\Http\Requests\StoreInvitationRequest;
use App\Http\Requests\UpdateInvitationRequest;
use Exception;
use Illuminate\Http\Request;
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
     * Métdo para criar convite
     */
    public function store(StoreInvitationRequest $request) {
        //Pega o usuario  logado
        $user = Auth::guard('sanctum')->user();
        try {
            //verifica se o usuario tem autorizão para realizar a ação
            CustomException::authorizedActionException('invite-store', $user, "InvitationController::store");
            //pega os dados validados
            $data = $request->validated();
            $userId = $user->idusuario;
            //cria um apelido pro convite
            $slug = $this->service->generateSlug($data['titulo']);

            $data['idusuario'] = $userId;
            $data['slug'] = $slug;
            //verifica se ação ocorreu bem
            CustomException::actionException($this->repository->createInvitation($data));  
            return response()->json(['message' => 'Convite Criado'], 200);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvitationRequest $request, $slug) {
        $invitation = $this->service->getBySlug($slug);
        //Pega o usuario  logado
        $user = Auth::guard('sanctum')->user();
        
        //VERIFICAR SE O USUÁRIO QUE POSTOU É O MESMO QUE ATUALIZARÁ
        try {
            //verifica se o usuario tem autorizão para realizar a ação
            CustomException::authorizedActionException('invite-update', $user, $invitation);
            //pega os dados validados
            $data = $request->validated();
            $data['idconvite'] = $invitation->idconvite;

            if ($data['titulo'] != $invitation->titulo)
            {
                $data['slug'] = $this->service->generateSlug($data['titulo']);
            }
            //verifica se ação ocorreu bem
            CustomException::actionException($this->repository->updateInvitation($data));       
            return response()->json(['message' => 'Convite Atualizado'], 200);
        }catch (Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {
        $invitation = $this->service->getBySlug($slug);
        //Pega o usuario  logado
        $user = Auth::guard('sanctum')->user();
        
        try {
            //verifica se o usuario tem autorizão para realizar a ação
            CustomException::authorizedActionException('invite-delete', $user, $invitation);
            //verifica se ação ocorreu bem
            CustomException::actionException($this->repository->deleteInvitation($invitation->idconvite));   

            return response()->json(['message' => 'Convite Excluido'], 200);
        }catch (Exception $e) {
           return response()->json(['message' => $e->getMessage()], 403); 
        }
    }
    /**
     * Método para enviar e registrar email quando um usuario aceita um convite
     *
     * @param Request $request
     * @param [type] $slug
     * @return void
     */
    public function aceitarInvite(Request $request, $slug) {
        //Pegando os dados do usuario logado
        $user = Auth::guard('sanctum')->user();
        try {
            //Menssagem que o usuario ira enviar para quem fez o convite
            $message = $request->all()['mensagem'];
            //Pegando os dados de quem criou o convite
            $inviteUser = $this->repository->getUserInvite($slug);
            //Dados para salvar no banco
            $data = [
               'idusuario' => Auth::guard('sanctum')->id(),
               'idconvite' => $inviteUser[0]->idconvite,
               'idusuario_convite' => $inviteUser[0]->idusuario,
               'texto'     => $message,
               'status'    => false,
            ];
            //Salvando registro do email
            $id = $this->repository->registerEmail($data);
            //Enviando Email para o usuario que criou o convite
            Mails::sendInvite($message, $inviteUser[0]->nome, $inviteUser[0]->email, $user['email'], $user['nome'], $id);
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }
    }
    /**
     * Método para usuario aceita o usuario do convite
     *
     * @param [type] $idemail
     * @return 'view'
     */
    public function emailAceito($idemail) {
        //deifine o status do email como true
       $data = ['status' => true];
        try {
            //verifica se ação ocorreu bem
            CustomException::actionException($this->repository->updateEmail($data, $idemail));
            //retorna uma view para o usuario
            return view('mails');
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

    }
}