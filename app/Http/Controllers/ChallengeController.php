<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
    private Challenge  $challenge;
    
    public function __construct() {
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
        $data = $request->all();
        $invite = $this->tratamenteDataInvite($data);
        $id = $this->challenge->createInvite($invite);
        $challege = $this->tratamenteDataChallenge($data, $id);

        $this->challenge->createChallenge($data);
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
    public function update(Request $request, Challenge $challenge)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Challenge $challenge)
    {
        //
    }

    public function tratamenteDataInvite($data) {
        $data_invite = [
            'idusuario'     => $data['idusuario'],
            'data_convite'  => $data['data_convite'],
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
            'imagem'  => $imagem,
            'idprofessor'     => $data['idprofessor']
        ];
        return $data_challerge;
    }
}
