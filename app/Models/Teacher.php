<?php

namespace App\Models;

use App\Models\User;
use Auth;
use Illuminate\Support\Facades\DB;
use Log;
/**
 * Classe 
 */
class Teacher extends User
{
    protected $table = "professor";
    const UPDATED_AT = 'atualizado_em';

    public function challenge()
    {
        return $this->hasMany(Challenge::class, 'idusuario');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'idusuario');
    }

    public function createTeacher($data)
    {
        $idUser = parent::createUser($data);
        if(!DB::table('professor')->insert(['idusuario' => $idUser])) {
            Log::error(self::class. "Error Create", ['dados: ' => $data, $GLOBALS['request'], Auth::guard('sanctum')->user()]);
            return false;
        }
        return true;
    }

    public function authorizeTeacher($idTeacher) 
    {
        if (!Teacher::where('idusuario', $idTeacher)->update(['autenticado' => 1])) {
            throw new HttpException(403, 'Não foi possivel autenticar o professor');
        }
    }

    public function getUnauthenticatedTeachers() {

    }
}