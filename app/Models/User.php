<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
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
    public function createUser($data) {
        
        return $this->create($data);
    }
    
    /**
     * Função de atualização do Usuario
     */
    public function updateUser() {
        
    }
    
    public function deleteUser() {

    }  

    use HasFactory;
}
