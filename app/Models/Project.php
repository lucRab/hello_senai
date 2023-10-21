<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Project extends Model
{
    protected $fillable = [
        'idusuario',
        'data_projeto',
        'descricao',
        'nome_projeto',
        'status'
    ];
    protected $table = "projeto";
    protected $primaryKey = "idproject";
    
    public $timestamps = false;
    /**
     * Função para criar projeto
     * @param [array] $data
     * @return int $idprojeto
     */
    public function createProjects($data) {
        
        return $this->insertGetId($data);
    }
    /**
     * Funções de atualização de projeto
     *
     * @param [string] $id
     * @param [array] $data
     * @return void
     */
    public function updateProjects($id,$data) {
        return $this->where('idprojeto','=', $id)->update($data);
    }

    public function getAll() {

    }
    use HasFactory;
}
