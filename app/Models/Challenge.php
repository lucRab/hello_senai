<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Challenge extends Model
{
    use HasFactory;
    protected $primaryKey = 'iddesafio';
    protected $table = 'desafio';
    protected $fillable = [
        'titulo',
        'descricao',
        'data_convite',
        'idusuario',
        'slug',
        'idprofessor',
        'idconvite',
        'imagem',
    ];
    public const UPDATED_AT = 'atualizado_em';

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario', 'idusuario');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'iddesafio', 'iddesafio');
    }
    /**
     * Método de criação de desafio
     *
     * @param [array] $data
     * @return bool
     */
    public function createChallenge($data)
    {
        if(DB::table('desafio')->insert($data)) {
            return true;
        }
        Log::error(self::class . "Error Delete", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para atualizar o desafio
     *
     * @param [string] $idprofessor
     * @param [string] $idconvite
     * @param [array] $data
     * @return bool
     */
    public function updateChallenge($data, $idChallenge)
    {
        if (!Challenge::where('iddesafio', $idChallenge)->update($data)) {
            throw new HttpException(403, 'Não foi possível atualizar o desafio');
        }
    }
    /**
     * Método para deletar o desafio
     *
     * @param [string] $idconvite
     * @param [string] $idprofessor
     * @return bool
     */
    public function deleteChallenge($idchallenge)
    {
        if(!Challenge::where('iddesafio', $idchallenge)->delete()) {
            Log::error(self::class . "Error Delete", ['idConvite: ' => $idconvite,
            'idprofessor' => $idprofessor, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
            throw new HttpException(403, 'Não foi possível deletar o desafio, tente novamente mais tarde');
        }
    }

    public function getbySlug($slug)
    {
        $get = DB::table('desafio as d')
        ->where('d.slug', '=', $slug)->first();
        return $get;
    }
}