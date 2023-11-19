<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Invitation;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'nome',
        'email',
        'senha'
    ];
    protected $hidden = [
        'senha'
    ];
    protected $table = "usuario";
    protected $primaryKey = "idusuario";
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';
    /**
     * Função para criação do usuario;
     * @param $data - 
    */

    public function project()
    {
        return $this->hasMany(Project::class);
    }

    public function invite()
    {
        return $this->hasMany(Invitation::class);
    }
    public function challenge()
    {
        return $this->hasMany(Challenge::class);
    }
    public function createUser($data) {
        $data['data_criacao'] = \Carbon\Carbon::now();
        $id = $this->insertGetId($data);
        $this->generateUsername($data['nome'], $id);
        return $id;
    }
    
    /**
     * Função de atualização do Usuario;
     * @param $data  dados do usuario
     * @param $id  id do usuario
     * @return bool
     */
    public function updateUser($data,$id) {
        if($this->where('idsuario',$id)->update($data))  {
            return true;
        }
        Log::error(self::class. "Error Update", ['id usuario: ' => $id, 'dados' => $data,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }

    /**
     * Função para deletar o usuario;
     * @param $id id do usuario
     * @return bool
     */
    public function deleteUser($id) {
        if($this->where('idusuario', $id)->delete()) {
            return true;
        }
        Log::error(self::class. "Error Delete", ['id usuario: ' => $id,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }

    /**
     * Função para gerar um apelido para o usuario;
     * @param $name nome do usuario
     * @param $user_id id do usuario
     * @return void
     */
    public function generateUsername($name, $user_id) {
        $username = $name . substr(sha1($name . $user_id), 0, 8);
        $value = ['apelido' => $username];
        DB::table('usuario')->where('idusuario', $user_id)->update($value);
    }

    /**
     * Função para desativar a conta do usuario;
     * @param $id id do usuario
     * @return bool
     */
    public function desativateUser($id) {
        if($this->where('idusuario', $id)->update(['status' => 'inativo'])) {
            return true;
        }
        Log::error(self::class. "Error desativate", ['id usuario: ' => $id,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false; 
    }
    
    /**
     * Função para selecionar o usuario pelo apelido;
     * @param $nickname apelido do usuario
     * @return array
     */
    public function getByNickname($nickname) {
        return $this->where('apelido', '=',$nickname)->get()->toArray();
    }

    /**
     * Função para selecionar todos os usuario;
     * @return array
     */
    public function getAll() {
        return $this->get()->toArray();
    }

    public function getAuthPassword() {
        return $this->senha;
    }

}