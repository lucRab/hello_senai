<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Invitation
{
    protected $table = "desafio";
    protected $primaryKey = null;

    /**
     * Método de criação de desafio
     *
     * @param [array] $data
     * @return int
     */
    public function createChallenge($data) {
        $id = $this->insertGetId($data);
        return $id;
    }

    public function updateChallenge($idprofessor, $idconvite, $data) {
        \DB::table('desafio as d')
        ->join('convite as c','d.idconvite','=','c.idconvite')
        ->where('idprofessor','=',$idprofessor,'and','idconvite', '=', $idconvite)
        ->update($data);
    }
    use HasFactory;
}
