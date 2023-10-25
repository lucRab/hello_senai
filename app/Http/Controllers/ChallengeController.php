<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;

class ChallengeController extends Controller
{
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
        $challege = $this->tratamenteDataInvite($data);
        
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
    public function tratamenteDataChallerge($data, $idInvite) {
        $data_challerge = [
            'idconvite'     => $idInvite,
            'imagem'  => $data['imagem'],
            'idprofessor'     => $data['idprofessor']
        ];
        return $data_challerge;
    }
}
