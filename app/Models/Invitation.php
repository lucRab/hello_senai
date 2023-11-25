<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Support\Facades\DB;
use App\Http\Resources\V1\ProjectResource;
use Log;

class Invitation extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'titulo',
        'descricao',
        'data_convite',
        'idusuario',
        'slug'
    ];
    
    protected $table = "convite";
    protected $primaryKey = "idconvite";
    const UPDATED_AT = "data_atualizado";
    
    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }

    /**
     * Método para criar projeto
     * @param [array] $data
     * @return int $idprojeto
     */
    public function createInvitation(array $data) { 
        if ($this->insert($data)) {
            return true;
        }
        Log::error(self::class. "Error Create", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para atualizar o convite
     *
     * @param [array] $data
     * @return bool
     */
    public function updateInvitation(array $data)
    {
        $idInvitation = $data['idconvite'];
        if ($this->where('idconvite', '=', $idInvitation)->update($data))
        {
            return true;
        };
        Log::error(self::class. "Error Update", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para deletar o convite
     *
     * @param [string] $idInvitation
     * @return bool
     */
    public function deleteInvitation(string $idInvitation)
    {
        if ($this->where('idconvite', '=', $idInvitation)->delete())
        {
            return true;
        };
        Log::error(self::class. "Error Delete", ['idComentario: ' => $idInvitation, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para selecionar o usuario que criou o convite
     *
     * @param string $slug
     * @return array
     */
    public function getUserInvite(string $slug) {
        return DB::table('convite as c')
        ->join('usuario as u','c.idusuario', '=', 'u.idusuario')
        ->where('slug', '=', $slug)
        ->get(['u.nome','u.email', 'u.idusuario', 'c.idconvite'])
        ->toArray();
    }

    public function registerEmail(array $data) {
        if(DB::table('registro_email')->insert($data)) return true;
        return false;
    }
}