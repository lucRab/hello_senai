<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use App\Services\ChallengeService;
use App\Services\CustomException;
use Auth;
use Exception;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    private Challenge  $challenge;
    private $service;
    
    public function __construct() {
        $this->service = new ChallengeService();
        $this->challenge = new Challenge();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $data = $request->all();
        try {
            CustomException::authorizedActionException('challenge-create', $user);
            
            $invite = $this->tratamenteDataInvite($data);
            $userId = $user->idusuario;
            $slug = $this->service->generateSlug($data['titulo']);

            $invite['idusuario'] = $userId;
            $invite['slug'] = $slug;
            
            CustomException::actionException($id = $this->challenge->createInvitation($invite));
            
            $challege = $this->tratamenteDataChallenge($data, $id);
            $challege['idprofessor'] = $userId;

            if($data['imagem']) {
                $extension = $data['imagem']->getClientOriginalExtension();
                $challege['imagem'] = $data['imagem']->storeAs('projects', $invite['slug'].now().$extension);
            }
            CustomException::actionException($this->challenge->createChallenge($challege));
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
    public function update(Request $request, $slug)
    {   
        $desafio = $this->service->getBySlug($slug);
        $user = Auth::guard('sanctum')->user();
        try {
            CustomException::authorizedActionException('challenge-update', $user, $desafio);
            
            $data = $request->all();
            $idchallenge = $data['iddesafio'];
            if(!empty($data['imagem'])) {
                $extension = $data['imagem']->getClientOriginalExtension();
                $img = $data['imagem']>storeAs('projects', $data['slug'].now().$extension);
            }
            $challenge = $this->tratamenteDataInvite($data);
            
            CustomException::actionException($this->challenge->updateChallenge(Auth::guard('sanctum')->id(), $idchallenge,$challenge, $img));
        }catch(Exception $e) {
            return response()->json(['message' => $e->getMessage()], 403); 
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($slug)
    {
        $user = Auth::guard('sanctum')->user();
        $get = $this->challenge->getbySlug($slug);
        try{
            CustomException::authorizedActionException('challenge-delete', $user, $get);
            CustomException::actionException($this->challenge->deleteChallenge($get[0]->idconvite, $get[0]->idprofessor));
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
    public function tratamenteDataChallenge($data, $idInvite) {
        if(!empty($data['imagem'])) {
            $imagem = $data['imagem'];
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
