<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
    //Método Override da classe Invite
    public function createInvite($data) {
        $id = DB::table('convite')->insertGetId($data);
        return $id;
    }

    public function updateChallenge($idprofessor, $idconvite, $data) {
        DB::table('desafio as d')
        ->join('convite as c','d.idconvite','=','c.idconvite')
        ->where('idprofessor','=',$idprofessor,'and','idconvite', '=', $idconvite)
        ->update($data);
    }

    public function deleteChallenge($idconvite, $idprofessor) {
        $this->where('idconvite','=',$idconvite, 'and', 'idprofessor', '=', $idprofessor)
        ->delete();

        $this->deleteInvite($idconvite);
    }
    use HasFactory;
}
