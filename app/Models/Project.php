<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens; 
use Illuminate\Support\Facades\DB;
use App\Http\Resources\V1\ProjectResource;

class Project extends Model
{

    protected $fillable = [
        'idusuario',
        'descricao',
        'nomeProjeto',
        'status',
        'slug',
        'desafio'
    ];

    //Variaveis de definição da tabela
    protected $table = "projeto";
    protected $primaryKey = "idprojeto";
    const UPDATED_AT = "data_atualizado";
    public Comment $comentario;

    public function __construct() {
        $this->comentario = new Comment();
    }

    /**
     * Método para realizar relacionamento com o usuario
     * @return object $data
     */
    public function user() {
        return $this->belongsTo(User::class, 'idusuario');
    }

    public function participants()
    {
        return $this->hasManyThrough(
            User::class,
            Permission::class,
            'idprojeto', // Foreign key on permissions table...
            'idusuario', // Foreign key on users table...
            'idprojeto', // Local key on projects table...
            'idusuario'  // Local key on permissions table...
        );
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'idprojeto');
    }

    public function challenge()
    {
        return $this->hasOne(Challenge::class, 'iddesafio', 'iddesafio');
    }

    public function denounces()
    {
        return $this->hasMany(Denounce::class, 'idprojeto', 'idprojeto');
    }

    /**
     * Método para criar projeto
     * @param [array] $data
     * @return int $idprojeto
     */
    public function createProject($data) { 
        $id = $this->insertGetId($data);
        return $id;
    }
    /**
     * Método para atualizar o projeto
     *
     * @param [array] $data
     * @return bool
     */
    public function updateProject($data)
    {
        $idProject = $data['idprojeto'];
        if ($this->where('idprojeto', '=', $idProject)->update($data))
        {
            return true;
        };
        Log::error(self::class. "Error Update", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        throw new \Exception('dsadsds');
    }
    /**
     * Método para deletar o projeto
     *
     * @param [string] $idProject
     * @return bool
     */
    public function deleteProject($idProject)
    {
        if ($this->where('idprojeto', '=', $idProject)->delete())
        {
            return true;
        };
        Log::error(self::class. "Error Delete", ['id Projeto: ' => $idProject,
        $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }

    public function getProject(string $id) {
        return $this->where('idprojeto', '=', $id)->get()->toArray();
    }
    /**
     * Método para inserir o link do github do projeto
     *
     * @param [array] $data
     * @return bool
     */
    public function linkGit($data) {
        if(DB::table('link')->insertGetId($data)) return true;
        Log::error(self::class. "Error Insert", ['Dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para denuciar o projeto
     *
     * @param [array] $data
     * @return bool
     */
    public function reportProject($data) {
        if(DB::table('denuncia')->insert($data)) return true;
        Log::error(self::class. "Error Denuncia", ['Dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }
    /**
     * Método para vincular um projeto a um desafio
     *
     * @param [array] $idDesafio
     * @param [string] $idProjeto
     * @return bool
     */
    public function vinculationChallenge(array $idDesafio,string $idProjeto) {
        if($this->where('idprojeto', '=', $idProjeto)->update($idDesafio)) return true;
        Log::error(self::class. "Error Create", ['id Projeto: ' => $idProjeto, $idDesafio, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
        return false;
    }

    public function addRangeParticipants($participants)
    {
        if (DB::table('permissao')->insert($participants)) return true;
        return false;
    }
}