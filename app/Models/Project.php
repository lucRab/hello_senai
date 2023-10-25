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

    //Variaveis de definição da tabela
    protected $table = "projeto";
    protected $primaryKey = "idprojeto";
    
    public $timestamps = false;
    /**
     * Método para criar projeto
     * @param [array] $data
     * @return int $idprojeto
     */
    public function createProjects($data) {
        
        $id = $this->insertGetId($data);
        $this->generateUniquiName($data['nome_projeto'],$id);
    }
    /**
     * Método para criar um nome unico para o projeto
     *
     * @param [string] $name_project
     * @param [int] $idprojeto
     * @version ${1:1.0.0
     * @return void
     */
    private function generateUniquiName($name_project, $idprojeto) {
        
        $newname = $name_project . substr(sha1($name_project . $idprojeto), 0, 6);
        $value = ['nome_projeto' => $newname];
        $this->updateProjects($idprojeto,$value);
    }
    /**
     * Método de atualização de projeto
     *
     * @param [string] $id
     * @param [array] $data
     * @return void
     */
    public function updateProjects($idproject, $data) {
        return $this->where('idprojeto','=', $idproject)->update($data);
    }
    /**
     * Método para deletar o projeto
     *
     * @param [array] $id
     * @return void
     */
    public function deleteProjects($id) {
        return $this->where('idprojeto',$id)->delete();
    }

    /**
     * Método para selecuinar todos os projetos
     *
     * @return array
     */
    public function getAll() {
        return $this->get()->toArray();
    }

    public function getAllProjectUser() {

    }
    /**
     * Método para selecionar o projeto pelo nome
     *
     * @param string $name
     * @return void
     */
    public function getByName(String $name) {
        return $this->where('nome_projeto',$name)->get()->toArray();
    }
    use HasFactory;
}
