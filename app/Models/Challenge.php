<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;

class Challenge extends Invitation
{
    protected $primaryKey = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }
    /**
     * Método de criação de desafio
     *
     * @param [array] $data
     * @return bool
     */
    public function createChallenge($data) {
        if(DB::table('desafio')->insert($data)) {
            return true;
        }
        Log::error(self::class. "Error Delete", ['dados: ' => $data,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }
    /**
     * Método para atualizar o desafio
     *
     * @param [string] $idprofessor
     * @param [string] $idconvite
     * @param [array] $data
     * @return bool
     */
    public function updateChallenge(string $idprofessor, string $idconvite, $data, string $img = null) {
        
            if(DB::table('convite as c')
            ->join('desafio as d','d.idconvite','=','c.idconvite')
            ->where('idprofessor','=',$idprofessor,'and','idconvite', '=', $idconvite)
            ->update($data)) {
                if($img != null) {
                    $this->where('idprofessor','=',$idprofessor,'and','idconvite', '=', $idconvite)->update(['imagem' => $img]);
                }
                return true;
            }
        Log::error(self::class. "Error Delete", ['idConvite: ' => $idconvite,
                                                'idprofessor' => $idprofessor,
                                                'dados' => $data,
                                                'browser' => $_SERVER["HTTP_USER_AGENT"],
                                                'URI' => $_SERVER["REQUEST_URI"],
                                                'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }
    /**
     * Método para deletar o desafio
     *
     * @param [string] $idconvite
     * @param [string] $idprofessor
     * @return bool
     */
    public function deleteChallenge(string $idconvite,string $idprofessor) {
        
        if(!DB::table('desafio')->where('idconvite','=',$idconvite, 'and', 'idprofessor', '=', $idprofessor)
        ->delete()) {
            Log::error(self::class. "Error Delete", ['idConvite: ' => $idconvite,
                                                     'idprofessor' => $idprofessor,
                                                     'browser' => $_SERVER["HTTP_USER_AGENT"],
                                                     'URI' => $_SERVER["REQUEST_URI"],
                                                     'Server' => $_SERVER["SERVER_SOFTWARE"]]);
            return false;
        }

        $this->deleteInvitation($idconvite);
        
        return true;
    }
    public function createInvitation(array $data) { 
        $id = DB::table('convite')->insertGetId($data);
        return $id;
    
    }
    public function getbySlug($slug) {
        $get = DB::table('desafio as d')
        ->join('convite as c','d.idconvite','=','c.idconvite')
        ->where('c.slug', '=', $slug)->get(['d.idprofessor','c.idconvite'])->toArray();
        return $get;
    }

    public function deleteInvitation(string $idInvitation)
    {
        if (DB::table('convite')->where('idconvite', '=', $idInvitation)->delete())
        {
            return true;
        };
        Log::error(self::class. "Error Delete", ['idComentario: ' => $idInvitation,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }
    use HasFactory;
}
