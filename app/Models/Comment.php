<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Database\Eloquent\Model;
use Log;

class Comment extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "comentario";
    protected $primaryKey = "idcomentario";
    protected $fillable = [
        'idprojeto',
        'texto',
        'idresposta',
        'idusuario',
        'criado_em'
    ];

    const CREATED_AT = 'criado_em';
    const UPDATED_AT = null;

    public function project() {
        return $this->belongsTo(Project::class, 'idprojeto');
    }

    public function user() {
        return $this->belongsTo(User::class, 'idusuario');
    }

    public function reply() {
        return $this->belongsTo(Comment::class, 'idresposta');
    }

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
        Log::error(self::class. "Error Create", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
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
        Log::error(self::class. "Error Update", ['idComentario: ' => $idComment, 'dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
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
        Log::error(self::class. "Error Delete", ['idComentario: ' => $idComment, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
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
}