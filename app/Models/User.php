<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Support\Facades\DB;
use App\Models\Project;
use App\Models\Invitation;
use App\Models\Teacher;
use Symfony\Component\HttpKernel\Exception\HttpException;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    
    protected $fillable = [
        'nome',
        'email',
        'senha',
        'apelido',
        'status'
    ];
    protected $hidden = [
        'senha'
    ];
    protected $table = "usuario";
    protected $primaryKey = "idusuario";
    const CREATED_AT = 'data_criacao';
    const UPDATED_AT = 'data_atualizacao';
   /**
    * Método para vincular o projeto ao usuario
    *
    */
    public function project()
    {
        return $this->hasMany(Project::class, 'idusuario');
    }
    /**
     * Método para vincular o convite ao usuario
     *
     */
    public function invite()
    {
        return $this->hasMany(Invitation::class, 'idusuario');
    }
    /**
     * Método para vincular o desafio ao usuario
     *
     */
    public function challenge()
    {
        return $this->hasMany(Challenge::class);
    }

    public function teacher()
    {
        return $this->hasMany(Teacher::class, 'idusuario');
    }

    public function comments() {
        return $this->hasMany(Comment::class, 'idusuario');
    }

     /**
     * Função para criação do usuario;
     * @param $data - 
    */
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
    public function updateUser($data, $id) {
        if($this->where('idusuario', $id)->update($data))  {
            return true;
        }
        Log::error(self::class. "Error Update", ['id usuario: ' => $id, 'dados' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
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
        Log::error(self::class. "Error Delete", ['id usuario: ' => $id, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }

    /**
     * Função para gerar um apelido para o usuario;
     * @param $name nome do usuario
     * @param $userId id do usuario
     * @return void
     */
    public function generateUsername($name, $userId) {
        $username = $name . substr(sha1($name . $userId), 0, 8);
        $value = ['apelido' => $username];
        DB::table('usuario')->where('idusuario', $userId)->update($value);
    }

    /**
     * Função para desativar a conta do usuario;
     * @param $id id do usuario
     * @return bool
     */
    public function disable($id) {
        if(!$this->where('idusuario', $id)->update(['status' => 'inativo'])) {
            Log::error(self::class. "Error desativate", ['id usuario: ' => $id, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
            throw new HttpException(403, 'Não foi possível desativar sua conta');
        }
    }

    public function notifications($userId) {
        $data = Email::with('user')->where('idusuario_convite', $userId)->get();
        return $data;
    }
    
    /**
     * Função para selecionar o usuario pelo apelido;
     * @param $nickname apelido do usuario
     * @return array
     */
    public function getByNickname($nickname) {
        return $this->where('apelido', '=', $nickname)->get()->first();
    }

    public function getAuthPassword() {
        return $this->senha;
    }

}