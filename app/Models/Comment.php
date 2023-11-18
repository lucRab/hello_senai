<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Log;

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
        if($this->insert($data)) {
            return true;
        }
        Log::error(self::class. "Error Create", ['dados: ' => $data,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
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
        if($this->where('idcomentario', '=', $idComment)->update($data)) {
            return true;
        }
        Log::error(self::class. "Error Update", ['idComentario: ' => $idComment, 'dados: ' => $data,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
        return false;
    }
    /**
     * Método para deletar o comentario
     *
     * @param [array] $idComment
     * @return bool
     */
    public function deleteComment($idComment) {
        if($this->where('idcomentario', '=', $idComment)->delete()) {
            return true;
        }
        Log::error(self::class. "Error Delete", ['idComentario: ' => $idComment,
        'browser' => $_SERVER["HTTP_USER_AGENT"],
        'URI' => $_SERVER["REQUEST_URI"],
        'Server' => $_SERVER["SERVER_SOFTWARE"]]);
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
