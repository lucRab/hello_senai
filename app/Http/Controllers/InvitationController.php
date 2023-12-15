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
use Symfony\Component\HttpKernel\Exception\HttpException;
use App\Filters\V1\InvitesFilter;


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
    public function index(Request $request)
    {
        $filter = new InvitesFilter();
        $queryItems = $filter->transform($request);
        $order = $request->query('order') ?? 'DESC';
        $limit = $request->query('limit') ?? 15;

        if (empty($queryItems)) {
            $invitations = $this->repository->with('user')->orderBy('data_convite', $order)->paginate($limit);
            return InvitationResource::collection($invitations);
        }

        $invitations = $this->repository->with('user')->where($queryItems)->orderBy('data_convite', $order)->paginate($limit);
        return InvitationResource::collection($invitations);
    }

    /**
     * Métdo para criar convite
     */
    public function store(StoreInvitationRequest $request) {
        //Pega o usuario  logado
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                //pega os dados validados
                $data = $request->validated();
                $userId = $user->idusuario;
                //cria um apelido pro convite
                $slug = $this->service->generateSlug($data['titulo']);

                $data['idusuario'] = $userId;
                $data['slug'] = $slug;
                //verifica se ação ocorreu bem
                $this->repository->createInvitation($data);  
                return response()->json(['message' => 'Convite Criado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        }catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode()); 
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvitationRequest $request, string $slug) {
        try {
            $invitation = $this->service->getBySlug($slug);

            if (!$invitation) {
                throw new HttpException(404, 'Convite não encontrado');
            }
            
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                if ($user->idusuario !== $invitation->idusuario) {
                    throw new HttpException(401, 'Autorização negada');
                }

                $data = $request->validated();
                CustomException::authorizedActionException('invite-update', $user, $invitation);
                $data['idconvite'] = $invitation->idconvite;

                if ($data['titulo'] !== $invitation->titulo)
                {
                    $data['slug'] = $this->service->generateSlug($data['titulo']);
                }
                //verifica se ação ocorreu bem
                CustomException::actionException($this->repository->updateInvitation($data));       
                return response()->json(['message' => 'Convite Atualizado'], 200);
            }
            throw new HttpException(401, 'Autorização negada');
        }catch (HttpException $e) {
            return response()->json(['message' => $e->getMessage()], $e->getStatusCode()); 
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug) {
        try {
            if (Auth::guard('sanctum')->check()) {
                $user = Auth::guard('sanctum')->user();
                $invite = $this->service->getBySlug($slug);
                if (!$invite) throw new HttpException(404, 'Convite não encontrado');
                if ($invite->idusuario === $user->idusuario) {
                    CustomException::authorizedActionException('invite-destroy', $user, $invite);
                    CustomException::actionException($this->repository->deleteInvitation($invite->idconvite));
                    return response()->json(['message' => 'Projeto Excluido'], 200);
                }
            }
            
            throw new HttpException(401, 'Autorização negada');
        } catch (\Exception $e) {
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
    public function acceptInvite(Request $request, $slug) {
        //Pegando os dados do usuario logado
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            try {
                //Menssagem que o usuario ira enviar para quem fez o convite
                $message = $request->all()['mensagem'];
                //Pegando os dados de quem criou o convite
                $inviteUser = $this->repository->getUserInvite($slug);
                
                if (!$inviteUser) {
                    return response()->json(['message' => 'Convite não encontrado'], 404);
                }

                if (Auth::guard('sanctum')->id() === $inviteUser->idusuario) {
                    return response()->json(['message' => 'Esse convite te pertence'], 403);
                }

                //Dados para salvar no banco
                $data = [
                   'idusuario' => Auth::guard('sanctum')->id(),
                   'idconvite' => $inviteUser->idconvite,
                   'destinatario' => $inviteUser->idusuario,
                   'texto'     => $message,
                   'status'    => 0,
                ];
                //Salvando registro do email
                $id = $this->repository->sendEmail($data);
                //Enviando Email para o usuario que criou o convite
                Mails::sendInvite($message, $inviteUser->nome, $inviteUser->email, $user['email'], $user['nome'], $id);
                return response()->json(['message' => 'Mensagem enviada'], 200);
            }catch(Exception $e) {
                return response()->json(['message' => $e->getMessage()], 403);
            }
        }
        return response()->json(['message' => 'Autorização negada'], 401);
    }

    public function ownerInviteAcceptUser($idemail) {
        //deifine o status do email como true
       $data = ['status' => 1];
        try {
            //verifica se ação ocorreu bem
            CustomException::actionException($this->repository->updateInviteEmail($data, $idemail));
            //retorna uma view para o usuario
            return view('mails');
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403);
        }

    }

}