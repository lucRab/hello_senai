<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class User extends Model
{
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'idusuario'
    ];
    
    //Variaveis de definição da tabela
    protected $hidden = [
        'senha'
    ];
    protected $table = "usuario";
    protected $primaryKey = "apelido";
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';
    /**
     * Método para criação do usuario;
     * @param $data - 
    */
    public function createUser($data) {
        $data['data_criacao'] = \Carbon\Carbon::now();
        $id = $this->insertGetId($data);
        $this->generateUsername($data['nome'], $id);
        return $id;
    }
    
    /**
     * Método de atualização do Usuario;
     * @param $data  dados do usuario
     * @param $id  id do usuario
     * @return void
     */
    public function updateUser($data,$id) {
        return $this->where('idsuario',$id)->update($data);
    }

    /**
     * Método para deletar o usuario;
     * @param $id id do usuario
     * @return void
     */
    public function deleteUser($id) {
        return $this->where('idusuario', $id)->delete();
    }

    /**
     * Método para gerar um apelido para o usuario;
     * @param $name nome do usuario
     * @param $user_id id do usuario
     * @return void
     */
    private function generateUsername($name, $user_id) {
        $username = $name . substr(sha1($name . $user_id), 0, 8);
        $value = ['apelido' => $username];
        DB::table('usuario')->where('idusuario', $user_id)->update($value);
    }

    /**
     * Método para desativar a conta do usuario;
     * @param $id id do usuario
     * @return void
     */
    public function desativateUser($id) {
        return $this->where('idusuario',$id)->update(['status' => 'inativo']);
    }
    
    /**
     * Método para selecionar o usuario pelo apelido;
     * @param $nickname apelido do usuario
     * @return array
     */
    public function getByNickname($nickname) {
        return $this->where('apelido', $nickname)->get()->toArray();
    }

    /**
     * Método para selecionar todos os usuario;
     * @return array
     */
    public function getAll() {
        return $this->get()->toArray();
    }

    /**
     * Método para criar especializar o usuario como professor;
     *
     * @return void
     */
    public function createProfessor($idusuario) {
        $data = [ 'idusuario' => $idusuario];
        DB::table('professor')->insert($data);
    }
    use HasFactory;
}
