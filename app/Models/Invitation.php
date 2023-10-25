<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invitation extends Model
{
    protected $fillable = [
        'idusuario',
        'data_convite',
        'descricao',
        'titulo'
    ];
    //Variaveis de definição da tabela
    protected $table = "convite";
    protected $primaryKey = "idconvite";
    public $timestamps = false;

    /**
     * Método para criar o convite
     *
     * @param [array] $data
     * @return int
     */
    public function  createInvite($data) {
        $id = $this->insertGetId($data);
        return $id;
    }

    /**
     * Método para atualizar o convite
     *
     * @param [string] $idinvite
     * @param [array] $data
     * @return void
     */
    public function updateInvite($idInvite,$data) {
        $this->where('idconvite', $idInvite)->update($data);
    }
    /**
     * Método para deletar o convite
     *
     * @param [string] $idInvite
     * @return void
     */
    public function deleteInvite($idInvite) {
        $this->where('idconvite', $idInvite)->delete();
    }
    use HasFactory;
}
