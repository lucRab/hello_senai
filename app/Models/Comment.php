<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $table = "comentario";
    protected $primaryKey = "idcomentario";
    protected $fillable = [
        'idprojeto',
        'texto',
        'idresposta',
        'idusuario'
    ];

    public $timestamps = false;
    /**
     * Método para criar o comentario
     *
     * @param [array] $data
     * @return bool
     */
    public function createComment($data) {
        if($this->insert($data)) return true;
        return false;
    }
    /**
     * Método para atualizar o comentario
     *
     * @param [string] $idComment
     * @param [array] $data
     * @return bool
     */
    public function updateComment($idComment, $data) {
        if($this->where('idcomentario', '=', $idComment)->update($data)) return true;
        return false;
    }
    /**
     * Método para deletar o comentario
     *
     * @param [array] $idComment
     * @return bool
     */
    public function deleteComment($idComment) {
        if($this->where('idcomentario', '=', $idComment)->delete()) return true;
        return false;
    }
    /**
     * Método para fazer o relacionamento com o comentario
     *
     * @return object
     */
    // public function reposta() {
    //     return $this->belongsTo(Comment::class,'idcomentario');
    // }


    use HasFactory;
}
